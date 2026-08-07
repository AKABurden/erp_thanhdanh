<style>
    #tb-payment-methods > thead > tr > th {
        border-top: 1px solid #93b4d6 !important;
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }
</style>
<div class="modal fade" id="paid_holiday_leave" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 65%">
        <div class="modal-content">
            <?php
            echo form_open_multipart(admin_url('paid_holidays/add_paid_holiday_leave/' . $id),
                array('id' => 'paid-holiday-form'));
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?= $title ?> </span>
                </h4>
            </div>
            <div class="modal-body" style="height:auto">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php $valueName = !empty($paidholiday) ? $paidholiday['name'] : '' ?>
                            <label for="name" class="control-label bold"><?= lang('Tên phiếu') ?></label>
                            <input type="text" class="form-control name" name="name" id="name"
                                   value="<?= $valueName ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_agree" class="control-label bold"><?= lang('Người duyệt') ?></label>
                            <?php $arrSelect[] = !empty($paidholiday) ? $paidholiday['staff_agree'] : '' ?>
                            <select class="selectpicker staff_agree form-control" name="staff_agree" id="staff_agree"
                                    data-live-search="true"
                                    title='<?php echo _l('Nhân viên'); ?>'
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            >
                                <?php if (!empty($staff)) { ?>
                                    <?php foreach ($staff as $key => $value) { ?>
                                        <optgroup label="<?= $value['name'] ?>">
                                            <?php if (!empty($value['staffs'])) : ?>
                                                <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                    <option data-subtext="<?= $v['name_roles'] ?>" <?= (!empty($arrSelect) && in_array($v['staffid'],
                                                            $arrSelect)) ? 'selected' : '' ?>
                                                            value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </optgroup>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_id" class="control-label bold"><?= lang('Nhân viên') ?></label>
                            <?php $arrSelect = [];
                            $arrSelect[] = !empty($paidholiday) ? $paidholiday['staff_id'] : '' ?>
                            <select class="selectpicker staff_id form-control" name="staff_id" id="staff_id"
                                    data-live-search="true"
                                    onchange="changePersonel(this)"
                                    title='<?php echo _l('Nhân viên'); ?>'
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            >
                                <?php if (!empty($staff)) { ?>
                                    <?php foreach ($staff as $key => $value) { ?>
                                        <optgroup label="<?= $value['name'] ?>">
                                            <?php if (!empty($value['staffs'])) : ?>
                                                <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                    <option data-subtext="<?= $v['name_roles'] ?>" <?= (!empty($arrSelect) && in_array($v['staffid'],
                                                            $arrSelect)) ? 'selected' : '' ?>
                                                            value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </optgroup>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_id_replace"
                                   class="control-label bold"><?= lang('Nhân viên thay thế (nếu có)') ?></label>
                            <?php $arrSelect = [];
                            $arrSelect[] = !empty($paidholiday) ? $paidholiday['staff_id_replace'] : '' ?>
                            <select class="selectpicker staff_id_replace form-control" name="staff_id_replace"
                                    id="staff_id_replace"
                                    data-live-search="true"
                                    title='<?php echo _l('Nhân viên thay thế'); ?>'
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            >
                                <?php if (!empty($staff)) { ?>
                                    <?php foreach ($staff as $key => $value) { ?>
                                        <optgroup label="<?= $value['name'] ?>">
                                            <?php if (!empty($value['staffs'])) : ?>
                                                <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                    <option data-subtext="<?= $v['name_roles'] ?>" <?= (!empty($arrSelect) && in_array($v['staffid'],
                                                            $arrSelect)) ? 'selected' : '' ?>
                                                            value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </optgroup>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="deparment" class="control-label bold"><?= lang('Bộ phận') ?></label>
                            <div class="name_deparment"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address" class="control-label bold"><?= lang('Địa chi') ?></label>
                            <div class="name_address"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="phone_number" class="control-label bold"><?= lang('Số điện thoại') ?></label>
                            <div class="name_phone_number"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="month" class="control-label bold"><?= lang('Tháng') ?></label>
                            <select class="selectpicker month form-control" name="month" multiple id="month"
                                    data-live-search="true"
                                    onchange="changeMonth(this)"
                                    title='<?php echo _l('Tháng'); ?>'
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            >
                                <?php foreach (getMonth() as $k => $v) : ?>
                                    <?php if ($k == '') {
                                        continue;
                                    } ?>
                                    <option <?= (!empty($arrSelect) && in_array($v,
                                            $arrSelect)) ? 'selected' : ($k == date('m') ? 'selected' : '') ?>
                                            value="<?= $k ?>"><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="year" class="control-label bold"><?= lang('Năm') ?></label>
                            <select class="selectpicker year form-control" name="year" id="year"
                                    data-live-search="true"
                                    onchange="changeYear(this)"
                                    title='<?php echo _l('Năm'); ?>'
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            >
                                <?php foreach (getYear() as $k => $v) : ?>
                                    <option <?= (!empty($arrSelect) && in_array($v,
                                            $arrSelect)) ? 'selected' : ($k == date('Y') ? 'selected' : '') ?>
                                            value="<?= $k ?>"><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row col-md-12" style="display: flex;align-items: center">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="type_magic_new" class="control-label bold"><?= lang('Loại phép') ?></label>
                                <select class="type_magic_new modal-select2 selectpicker"
                                        data-live-search="true"
                                        title='<?php echo _l('Loại phép'); ?>'>
                                    <?php foreach ($typeMagic as $key => $value) { ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="date_paid_holiday"
                                       class="control-label bold"><?= lang('Ngày nghỉ phép') ?></label>
                                <select class="date_paid_holiday modal-select2 selectpicker"
                                        data-live-search="true"
                                        multiple
                                        title='<?php echo _l('Ngày nghỉ phép'); ?>'>
                                    <?php foreach ($allDateNew as $key => $value) { ?>
                                        <option value="<?= $value['date'] ?>"
                                                data-subtext="<?= $value['day'] ?>"><?= $value['date'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" onclick="chosenDate(this)">Chọn</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6 bold">Phép năm <?= date('Y') ?> : <span class="html_paid_holiday bold" style="color: red;font-size: 15px"></span></div>
                    <div class="col-md-6 bold">Phép năm <?= date('Y') - 1 ?> : <span class="html_paid_holiday_old bold" style="color: red;font-size: 15px"></span></div>
                    <div class="col-md-12">
                        <div style="color: green;margin-top: 10px;font-size: 16px">Loại nghỉ phép</div>
                        <table class="table table-hover" style="margin-top: 10px" id="tb-payment-methods">
                            <thead>
                            <tr>
                                <th rowspan="2" class="text-center" style="width: 30px;">
                                    <a class="hover-svg hide" onclick="addPaymentMethods()">
                                        <svg width="20" height="20" viewBox="0 0 30 30" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"/>
                                            <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z"
                                                  fill="white"/>
                                        </svg>
                                    </a>
                                </th>
                                <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Loại phép') ?></th>
                                <th class="text-center" colspan="2" style="width: 100px;"><?= lang('Thời gian') ?></th>
                                <th class="text-center" rowspan="2"
                                    style="width: 100px;"><?= lang('Số ngày nghỉ') ?></th>
                                <th class="text-center" rowspan="2"
                                    style="width: 100px;"><?= lang('Ngày làm việc lại') ?></th>
                                <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Ghi chú') ?></th>
                                <th class="text-center" rowspan="2" style="width: 30px;"><span
                                            class="fa fa-trash-o"></span></th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 100px;"><?= lang('Từ ngày') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Đến ngày') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $counterPM = 0;
                            if (!empty($paidholidayDetai)) { ?>
                                <?php foreach ($paidholidayDetai as $key => $value) { ?>
                                    <?php
                                    $htmlTypeMagic = '';
                                    foreach ($typeMagic as $kk => $val) {
                                        $htmlTypeMagic .= '<option ' . ($val['id'] == $value['type_magic_id'] ? 'selected' : '') . ' value="' . $val['id'] . '">' . $val['name'] . '</option>';
                                    }

                                    $paidholidayDetaiMonth = get_table_where('tbl_paid_holiday_leave_detail_month',
                                        ['paid_holiday_leave_detail_id' => $value['id']]);
                                    ?>
                                    <tr class="edit_date" data-date="<?= _dhau($value['date_end']) ?>">
                                        <td class="text-center stt"></td>
                                        <td>
                                            <input type="hidden" name="pm[<?= $counterPM ?>][conter]" class="conter"
                                                   value="<?= $counterPM ?>">
                                            <input type="hidden" name="pm[<?= $counterPM ?>][id]"
                                                   value="<?= $value['id'] ?>">
                                            <select class="type_magic modal-select2 selectpicker"
                                                    data-live-search="true"
                                                    onchange="changeType(this);getTotal();"
                                                    title='<?php echo _l('Loại phép'); ?>'
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                    style="width: 100%;height: 30px"
                                                    name="pm[<?= $counterPM ?>][type_magic]"
                                                    id="type_magic<?= $counterPM ?>">
                                                <?= $htmlTypeMagic ?>
                                            </select>
                                            <div class="text_error_magic" style="color:red"></div>
                                        </td>
                                        <td>
                                            <div class="html_date_start"><?= _dhau($value['date_start']) ?></div>
                                            <input type="hidden" required onchange="getTotal();changeDate(this);"
                                                   name="pm[<?= $counterPM ?>][date_start]" autocomplete="off"
                                                   class="form-control date_start datepicker"
                                                   value="<?= _dhau($value['date_start']) ?>">
                                        </td>
                                        <td>
                                            <div class="html_date_end"><?= _dhau($value['date_end']) ?></div>
                                            <input type="hidden" required onchange="getTotal();changeDate(this);"
                                                   name="pm[<?= $counterPM ?>][date_end]" autocomplete="off" readonly
                                                   class="form-control none-event date_end datepicker"
                                                   value="<?= _dhau($value['date_end']) ?>">
                                        </td>
                                        <td class="td-date">
                                            <input type="hidden" required onchange="getTotal()"
                                                   name="pm[<?= $counterPM ?>][number_day]"
                                                   class="form-control number_day number-format"
                                                   value="<?= ($value['number_date']) ?>">
                                            <div class="sub">
                                                <?php if (!empty($paidholidayDetaiMonth)) { ?>
                                                    <?php foreach ($paidholidayDetaiMonth as $kk => $vv) { ?>
                                                        <div class="sb" style="display: flex;align-items: center">
                                                            <div class="col-md-7" style="padding: 0px;"><span
                                                                        class="bold"
                                                                        style="font-style: italic">Tháng <?= $vv['month'] ?></span><input
                                                                        type="hidden"
                                                                        name="month_sub[<?= $counterPM ?>][]"
                                                                        value="<?= $vv['month'] ?>" style="width: 100%;"
                                                                        title=""></div>
                                                            <div class="col-md-5" style="padding: 0px;"><input
                                                                        type="text" onchange="getTotalNew()" required
                                                                        style="width: 100%;"
                                                                        name="quantity_sub[<?= $counterPM ?>][]"
                                                                        id="input"
                                                                        class="form-control quantity_sub number-format"
                                                                        value="<?= $vv['number_day'] ?>"></div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" required name="pm[<?= $counterPM ?>][day_work]"
                                                   onchange="changeDateWork(this);getTotal()" autocomplete="off"
                                                   class="form-control day_work datepicker"
                                                   value="<?= _dhau($value['day_work']) ?>">
                                        </td>
                                        <td style="width: 120px" class="text-left">
                                            <textarea class="form-control note" name="pm[<?= $counterPM ?>][note]"
                                                      cols="2" rows="2"><?= $value['note'] ?></textarea>
                                        </td>
                                        <td><span class="fa fa-remove text-danger pointer"
                                                  onclick="removePaymentMethods(this)"></span></td>
                                    </tr>
                                    <?php $counterPM++;
                                } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    var counterPM = <?= !empty($counterPM) ? $counterPM : 0 ?>;
    var countError = 0;
    var dtTypeMagic = <?= !empty($typeMagic) ? json_encode($typeMagic) : '{}' ?>;
    var arrDateExist = [];

    <?php if (!empty($paidholiday)) { ?>
    $("#staff_id").trigger('change');
    getTotal();
    <?php } ?>
    <?php if (empty($paidholiday)){ ?>
    // addPaymentMethods();
    <?php } ?>
    function validate_form() {
        _validate_form($('#paid-holiday-form'), {
            staff_id: "required",
            name: "required",
            staff_agree: "required",
        }, add_payment);
    }

    init_selectpicker();
    $(function () {
        validate_form();
    });

    function add_payment(form) {
        url = form.action
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function (i, tag) {
            $.each($(tag)[0].files, function (i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function (i, val) {
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
            .done(function (data) {
                if (data.result) {
                    oTable.draw('page');
                    alert_float('success', data.message);
                    $('#paid_holiday_leave').modal('hide');
                } else {
                    alert_float('danger', data.message);
                }
            })
            .fail(function () {
                alert_float('danger', lang_core['errors']);
            });
        return false;
    }

    function changePersonel(_this) {
        personel_id = $(_this).val();
        year = $('#year').val();
        $(".name_deparment").html('');
        $(".name_address").html('');
        $(".name_phone_number").html('');
        if (personel_id) {
            dataString = {
                personel_id: personel_id,
                year: year,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>paid_holidays/getInfoByPersonel",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.personel) {
                        name_role = response.personel.name_role != null ? '(' + response.personel.name_role + ')' : '';
                        $(".name_deparment").html(response.personel.name_department != null ? response.personel.name_department : '' + name_role);
                        $(".name_address").html(response.personel.address != null ? response.personel.address : '');
                        $(".name_phone_number").html(response.personel.phone != null ? response.personel.phone : null);
                    }

                    $(".html_paid_holiday").html(response.number_date_phep);
                    $(".html_paid_holiday_old").html(response.number_date_phep_old);
                }
            });
            return false;
        }
    }

    function getTypeMagic(select_id) {
        var option = '<option value=""></option>';
        $.each(dtTypeMagic, function (index, el) {
            selected = select_id == el.id ? 'selected' : '';
            option += `<option ${selected} value="${el.id}">${el.name}</option>`;
        });
        return option;
    }


    function addPaymentMethods(type_magic_new = 0, date_paid = '') {
        nLength = $('#tb-payment-methods tbody tr').length;
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');

        var tdNumberPM = `<td class="stt text-center"></td>`;
        var tdTypeMagic = `<td>
            <input type="hidden" name="pm[${counterPM}][conter]" class="conter" value="${counterPM}">
            <select class="type_magic modal-select2 selectpicker"
             data-live-search="true"
             onchange="changeType(this);getTotal();"
            title='<?php echo _l('Loại phép'); ?>'
            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
            style="width: 100%;height: 30px" name="pm[${counterPM}][type_magic]" id="type_magic${counterPM}">
                ${getTypeMagic(type_magic_new)}
            </select>
            <div class="text_error_magic" style="color:red"></div>
        </td>`;
        var tdDateStart = `<td>
            <div class="html_date_start"></div>
           <input type="hidden" required onchange="getTotal();changeDate(this);" name="pm[${counterPM}][date_start]" autocomplete="off"  class="form-control date_start datepicker" value="${date_paid}">
        </td>`;
        var tdDateEnd = `<td>
            <div class="html_date_end"></div>
           <input type="hidden" required onchange="getTotal();changeDate(this);" name="pm[${counterPM}][date_end]" autocomplete="off"  class="form-control date_end datepicker" value="${date_paid}">
        </td>`;
        var tdNumberDayPM = `<td class="text-left td-date">
              <input type="hidden" required onchange="getTotal()" name="pm[${counterPM}][number_day]"  class="form-control number_day number-format" value="0">
              <div class="sub"></div>
        </td>`;
        var tdDayWork = `<td class="text-left ">
              <input type="text" required name="pm[${counterPM}][day_work]" onchange="changeDateWork(this);getTotal();" autocomplete="off"  class="form-control day_work datepicker" value="">
        </td>`;
        var tdNote = `<td style="width: 120px" class="text-left">
             <textarea class="form-control note" name="pm[${counterPM}][note]" cols="2" rows="2"></textarea>
        </td>`;
        var tdActionsPM = `<td class="text-center"><span class="fa fa-remove text-danger pointer" onclick="removePaymentMethods(this)"></span></td>`;

        var trPM = `<tr>
            ${tdNumberPM}
            ${tdTypeMagic}
            ${tdDateStart}
            ${tdDateEnd}
            ${tdNumberDayPM}
            ${tdDayWork}
            ${tdNote}
            ${tdActionsPM}
        </tr>`;

        $('#tb-payment-methods').append(trPM);
        // $(`#type_magic${counterPM}`).select2({
        //     allowClear:true
        // });
        $(`#type_magic${counterPM}`).attr('required', true);
        getTotal();
        init_datepicker();
        init_selectpicker();
        counterPM++;
    }

    function getTotal() {
        var tbPM = '#tb-payment-methods tbody tr:not("[class^=not-tr]")';
        var nPM = $(tbPM).length;
        var sttPM = 0;
        countError = 0;
        arrDateExist = [];
        for (iPM = 0; iPM < nPM; iPM++) {
            sttPM++;
            elementPM = $(tbPM)[iPM];
            $(elementPM).find('.stt').html(sttPM);
            type_magic = $(elementPM).find('select.type_magic').val();
            if (type_magic == '') {
                $(elementPM).find('.text_error_magic').html('Vui lòng chọn loại phép');
            } else {
                $(elementPM).find('.text_error_magic').html('');
            }
            // number_day = intVal($(elementPM).find('.number_day').val());
            date_start = $(elementPM).find('.date_start').val();

            $(elementPM).find('.date_end').val(date_start);

            date_end = $(elementPM).find('.date_end').val();
            day_work = $(elementPM).find('.day_work').val();
            conter = $(elementPM).find('.conter').val();
            date_end_vs1 = date_end;

            $(elementPM).find('.html_date_start').html(date_start);
            $(elementPM).find('.html_date_end').html(date_end);

            index = jQuery.inArray(date_start, arrDateExist);
            if (index !== -1) {
            } else {
                arrDateExist.push(date_start);
            }

            number_day = 0;
            $.each($(elementPM).find('.quantity_sub'), function (index, el) {
                number_day += intVal($(el).val());
            });

            if ((date_start != '' && date_start != undefined) && (date_end != '' && date_end != undefined)) {
                date_start = date_start.split('/');
                date_end = date_end.split('/');

                date_start_year = date_start[2];
                date_start_month = date_start[1];
                date_start_date = date_start[0];

                date_end_year = date_end[2];
                date_end_month = date_end[1];
                date_end_date = date_end[0];

                date_start_new = date_start_year + '-' + date_start_month + '-' + date_start_date;
                date_end_new = date_end_year + '-' + date_end_month + '-' + date_end_date;

                date_start_new_check = new Date(date_start_new);
                date_end_new_check = new Date(date_end_new);

                if (date_end_new_check < date_start_new_check) {
                    $(elementPM).find('.date_end').val('');
                    alert_float('danger', 'Không thể nhỏ hơn thời gian bắt đầu');
                }
                diff = minusTwoDate(date_start_new, date_end_new);
                if (isNaN(diff) == false) {
                    if (number_day > diff) {
                        alert_float('danger', 'Số ngày nghỉ không thể lớn hơn trong khoảng thời gian !');
                        // $(elementPM).find('.number_day').val(diff);
                    }
                    if ($(elementPM).hasClass('newVs1')) {
                        addNumberDay(date_end_month, date_start_month, conter, $(elementPM));
                    }
                    // $(elementPM).find('.number_day').val((diff));
                } else {
                    $(elementPM).find('.number_day').val(0);
                }
            } else {
                $(elementPM).find('.number_day').val(0);
            }

            if (day_work != '' && day_work != undefined) {
                day_work = day_work.split('/');
                date_end_vs1 = date_end_vs1.split('/');

                date_work_year = day_work[2];
                date_work_month = day_work[1];
                date_work_date = day_work[0];

                date_end_year = date_end_vs1[2];
                date_end_month = date_end_vs1[1];
                date_end_date = date_end_vs1[0];

                date_work_new = date_work_year + '-' + date_work_month + '-' + date_work_date;
                date_end_new = date_end_year + '-' + date_end_month + '-' + date_end_date;

                date_work_new_check = new Date(date_work_new);
                date_end_new_check = new Date(date_end_new);

                if (date_work_new_check < date_end_new_check) {
                    $(elementPM).find('.day_work').val('');
                    alert_float('danger', 'Không thể nhỏ hơn khoảng thời gian nghỉ');
                }
            }
        }
    }


    function getTotalNew() {
        var tbPM = '#tb-payment-methods tbody tr:not("[class^=not-tr]")';
        var nPM = $(tbPM).length;
        var sttPM = 0;
        countError = 0;
        for (iPM = 0; iPM < nPM; iPM++) {
            sttPM++;
            elementPM = $(tbPM)[iPM];
            $(elementPM).find('.stt').html(sttPM);
            date_start = $(elementPM).find('.date_start').val();
            date_end = $(elementPM).find('.date_end').val();
            day_work = $(elementPM).find('.day_work').val();
            conter = $(elementPM).find('.conter').val();
            date_end_vs1 = date_end;
            number_day = 0;
            $.each($(elementPM).find('.quantity_sub'), function (index, el) {
                number_day += intVal($(el).val());
            });

            if ((date_start != '' && date_start != undefined) && (date_end != '' && date_end != undefined)) {
                date_start = date_start.split('/');
                date_end = date_end.split('/');

                date_start_year = date_start[2];
                date_start_month = date_start[1];
                date_start_date = date_start[0];

                date_end_year = date_end[2];
                date_end_month = date_end[1];
                date_end_date = date_end[0];

                date_start_new = date_start_year + '-' + date_start_month + '-' + date_start_date;
                date_end_new = date_end_year + '-' + date_end_month + '-' + date_end_date;

                date_start_new_check = new Date(date_start_new);
                date_end_new_check = new Date(date_end_new);

                if (date_end_new_check < date_start_new_check) {
                    $(elementPM).find('.date_end').val('');
                    alert_float('danger', 'Không thể nhỏ hơn thời gian bắt đầu');
                }
                diff = minusTwoDate(date_start_new, date_end_new);
                if (isNaN(diff) == false) {
                    if (number_day > diff) {
                        alert_float('danger', 'Số ngày nghỉ không thể lớn hơn trong khoảng thời gian !');
                        $(elementPM).find('.quantity_sub').val(0);
                    }
                } else {
                    $(elementPM).find('.number_day').val(0);
                }
            } else {
                $(elementPM).find('.number_day').val(0);
            }
        }
    }

    function addNumberDay(month_end, month_start, counter, _this) {
        diff = (month_end - month_start) > 0 ? (month_end - month_start) : 0;
        var div = $(_this).find('.td-date');
        html = '';
        if (diff >= 0) {
            for (i = month_end; i >= month_start; i--) {
                i_new = i != month_end ? '0' + i : i;
                html += `<div class="sb" style="display: flex;align-items: center">
                <div class="col-md-7" style="padding: 0px;"><span class="bold" style="font-style: italic">Tháng ${i_new}</span><input type="hidden" name="month_sub[${counter}][]" value="${i_new}" style="width: 100%;" title=""></div>
                <div class="col-md-5" style="padding: 0px;"><input type="text" onchange="getTotalNew()" required style="width: 100%;" name="quantity_sub[${counter}][]" id="input" class="form-control quantity_sub number-format" value="0" ></div>
                </div>`;
            }
        }
        div.find('.sub').html(html);
    }

    function minusTwoDate(dateStartCal, dateEndCal) {
        if (!dateStartCal || !dateEndCal) {
            return 0;
        }
        var dateStartCal = new Date(dateStartCal);
        var dateEndCal = new Date(dateEndCal);
        var diffTime = Math.abs(dateEndCal.getTime() - dateStartCal.getTime());
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays + 1;
    }


    function checkExisitTotal(value, _this) {
        tb = '#tb-payment-methods tbody tr:not("[class^=current]")';
        var n = $(tb).length;
        for (ii = 0; ii < n; ii++) {
            element = $(tb)[ii];
            date_start = ($(element).find('.date_start').val());
            date_end = ($(element).find('.date_end').val());

            date_start_new_check = '';
            date_end_new_check = '';
            if ((date_start != '' && date_start != undefined) && (date_end != '' && date_end != undefined)) {
                date_start = date_start.split('/');
                date_end = date_end.split('/');

                date_start_year = date_start[2];
                date_start_month = date_start[1];
                date_start_date = date_start[0];

                date_end_year = date_end[2];
                date_end_month = date_end[1];
                date_end_date = date_end[0];

                date_start_new = date_start_year + '-' + date_start_month + '-' + date_start_date;
                date_end_new = date_end_year + '-' + date_end_month + '-' + date_end_date;

                date_start_new_check = new Date(date_start_new);
                date_end_new_check = new Date(date_end_new);
            }
            valueNew = value.split('/');
            value_year = valueNew[2];
            value_month = valueNew[1];
            value_date = valueNew[0];

            value_new = value_year + '-' + value_month + '-' + value_date;
            value_new_check = new Date(value_new);

            if (date_end_new_check != '' && date_end_new_check != undefined && date_start_new_check != '' && date_start_new_check != undefined) {
                if (value_new_check >= date_start_new_check && value_new_check <= date_end_new_check) {
                    alert_float('danger', 'Khoảng thời gian đã tồn tại');
                    $(element).closest('tbody').find('tr.current').find('.date_start').val('');
                    $(element).closest('tbody').find('tr.current').find('.date_end').val('');
                    $(element).closest('tbody').find('tr.current').find('.sub').html('');
                    $(element).closest('tbody').find('tr.current').remove();
                    $(element).closest('tbody').find('tr').removeClass('current');
                }
            }
        }
        $(_this).closest('tr').removeClass('current');
    }

    $(".date_start").change(function () {
        $(this).closest('tr').find('.date_end').trigger('change');
    });

    function changeDate(_this) {
        value = $(_this).val();

        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');
        $(_this).closest('tr').addClass('current newVs1');
        $(_this).closest('tr').attr('data-date', value);

        $(_this).closest('tr').find('.date_end').val('');

        $(_this).closest('tr').find('.date_end').val(value);
        $(_this).closest('tr').find('.date_end').attr('readonly', true);
        $(_this).closest('tr').find('.date_end').addClass('none-event');

        checkExisitTotal(value, _this);
    }

    function changeDateWork(_this) {
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');
        dateCheck = $(_this).val();
        dataString = {
            dateCheck: dateCheck,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>paid_holidays/checkSunday",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.result == 1){
                    alert_float('danger','Không được chọn ngày chủ nhật');
                    $(_this).val('');
                }
            }
        });
    }

    function changeType(_this) {
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');
    }

    function removePaymentMethods(_this) {
        $(_this).closest('tr').remove();
        getTotal();
    }

    function loadDateHoliday() {
        var month = $("#month").val();
        var year = $("#year").val();
        dataString = {
            month: month,
            year: year,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>paid_holidays/getListDate",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                htmlDate = '';
                if (response.allDateNew.length > 0) {
                    $.each(response.allDateNew, function (k, v) {
                        htmlDate += `<option value="${v.date}" data-subtext="${v.day}">${v.date}</option>`;
                    })
                }
                $("select.date_paid_holiday").html(htmlDate).selectpicker('refresh');
            }
        });
    }

    function changeMonth() {
        loadDateHoliday();
    }

    function changeYear() {
        loadDateHoliday();
    }

    function chosenDate() {
        type_magic_new = $("select.type_magic_new ").val();
        date_paid_holiday = $("select.date_paid_holiday ").val();
        if (date_paid_holiday.length == 0) {
            alert_float('danger', 'Vui lòng chọn ngày nghỉ phép');
            return;
        }
        if (!type_magic_new) {
            alert_float('danger', 'Vui lòng chọn loại nghỉ phép');
            return;
        }


        $.each(date_paid_holiday, function (k, v) {
            if (arrDateExist.includes(v)) {
                alert_float('danger', 'Ngày đã tồn tại');
                $("select.date_paid_holiday").val(0).selectpicker('refresh');
                return;
            }
            addPaymentMethods(type_magic_new, v);
            changeDateTable();
        })

        $("select.type_magic_new").val(0).selectpicker('refresh');
        $("select.date_paid_holiday").val(0).selectpicker('refresh');

        changeDateTable();
    }

    function changeDateTable() {
        var items = $('table#tb-payment-methods tbody').find('tr:not(".edit_date")');
        $.each(items, (index, value) => {
            $(value).find('.date_start').trigger('change');
        });
    }

</script>
