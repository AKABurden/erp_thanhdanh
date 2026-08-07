<style>
    #tb-payment-methods > thead > tr > th {
        border-top: 1px solid #93b4d6 !important;
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 200px !important;
    }

    .table-responsive-vs1 {
        max-height: 500px;
    }
    .dropdown-menu .open{
        max-height: 200px !important;
    }

    .content-bom {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }

    .table-responsive {
        max-height: 400px;
    }

</style>
<div class="modal fade" id="suggest_overtime" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <?php
            echo form_open_multipart(admin_url('suggest_overtime/add_suggest_overtime/' . $id),
                array('id' => 'suggest-overtime-form'));
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?= $title ?> </span>
                </h4>
            </div>
            <div class="modal-body" style="height:auto">
                <div class="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php $valueName = !empty($suggestOvertime) ? $suggestOvertime['name'] : '' ?>
                                <label for="name" class="control-label bold"><?= lang('Tên phiếu') ?></label>
                                <input type="text" class="form-control name" name="name" id="name"
                                       value="<?= $valueName ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="staff_id" class="control-label bold"><?= lang('Nhân viên') ?></label>
                                <?php $arrSelect[] = !empty($suggestOvertime) ? $suggestOvertime['staff_id'] : '' ?>
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
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php  $arrSelect = !empty($suggestOvertime) ? $suggestOvertime['month'] : '' ?>
                                <label for="month" class="control-label bold"><?= lang('Tháng') ?></label>
                                <select class="selectpicker month form-control" name="month" id="month"
                                        data-live-search="true"
                                        onchange="changeMonth(this)"
                                        title='<?php echo _l('Tháng'); ?>'
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                >
                                    <?php foreach (getMonth() as $k => $v) : ?>
                                        <option <?= ($arrSelect == $v ? 'selected' : (empty($arrSelect) && $k == date('m') ? 'selected' : '')) ?>
                                            value="<?= $k ?>"><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php $arrSelect = !empty($suggestOvertime) ? $suggestOvertime['year'] : ''; ?>
                                <label for="year" class="control-label bold"><?= lang('Năm') ?></label>
                                <select class="selectpicker year form-control" name="year" id="year"
                                        data-live-search="true"
                                        onchange="changeYear(this)"
                                        title='<?php echo _l('Năm'); ?>'
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                >
                                    <?php foreach (getYear() as $k => $v) : ?>
                                        <option <?= $arrSelect ==  $v ? 'selected' : (empty($arrSelect) && $k == date('Y') ? 'selected' : '') ?>
                                            value="<?= $k ?>"><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <label for="date_new" class="control-label bold"><?= lang('Chọn ngày') ?></label>
                            <input type="text" onchange="changdateNew(this)" autocomplete="off" class="form-control date_new datepicker" id="date_new">
                        </div>
                        <div class="col-md-12">
                            <div style="color: green;margin-top: 10px;font-size: 16px;margin-bottom: 10px">Chi tiết theo ngày</div>
                            <div class="table-responsive">
                                <table class="table table-hover" style="margin-top: 10px;width: 100%"
                                       id="tb-payment-methods">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 30px;">
                                        </th>
                                        <th class="text-center" style="width: 150px;"><?= lang('Ngày tăng ca') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('Số giờ tăng ca') ?></th>
                                        <th class="text-center" style="width: 150px;"><?= lang('Lý do') ?></th>
                                        <th class="text-center" style="width: 30px;"><span class="fa fa-trash-o"></span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $counterPM = 0;
                                    if (!empty($suggestOvertimeDetail)) { ?>
                                        <?php foreach ($suggestOvertimeDetail as $key => $value) { ?>
                                            <tr>
                                                <td class="text-center stt"></td>
                                                <td>
                                                    <div class="html_date"><?= _dhau($value['date']) ?></div>
                                                    <input type="hidden" name="pm[<?= $counterPM ?>][conter]" class="conter"
                                                           value="<?= $counterPM ?>">
                                                    <input type="hidden" name="pm[<?= $counterPM ?>][id]"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden"
                                                           name="pm[<?= $counterPM ?>][date]"
                                                           autocomplete="off"
                                                           class="form-control date_new_vs1 datepicker"
                                                           value="<?= _dhau($value['date']) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" onchange="getTotal();changeHourOvertime(this)" name="pm[<?= $counterPM ?>][hour_overtime]"  class="form-control hour_overtime number-format" value="<?= $value['hour_overtime'] ?>">
                                                </td>
                                                <td style="width: 120px" class="text-left">
                                                <textarea class="form-control note" name="pm[<?= $counterPM ?>][note]"
                                                          cols="2" rows="3"><?= $value['note'] ?></textarea>
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
    var allDate = <?= !empty($allDate) ? json_encode($allDate) : '{}' ?>;

    var arrId = [];

    <?php if (!empty($suggestOvertime)) { ?>
    // $("#staff_id").trigger('change');
    getTotal();
    <?php } ?>
    <?php if (empty($suggestOvertime)){ ?>
    addPaymentMethods();
    <?php } ?>
    function validate_form() {
        _validate_form($('#suggest-overtime-form'), {
            staff_id: "required",
            name: "required",
            year: "required",
            month: "required",
        }, add_payment);
    }


    init_selectpicker();
    $(function () {
        <?php if (empty($suggestOvertime)){ ?>
         startdate = "<?= date('Y-m-01') ?>";
         newdate = "<?= date("Y-m-t") ?>";
         date_default = "<?= date('Y-m-d') ?>";
        <?php } else { ?>
         startdate = "<?= date(''.$suggestOvertime['year'].'-'.$suggestOvertime['month'].'-01') ?>";
         newdate = "<?= date(''.$suggestOvertime['year'].'-'.$suggestOvertime['month'].'-t') ?>";
         date_default = startdate;
        <?php } ?>
        var date = new Date(`${date_default}`);
        date.setDate(date.getDate());
        $('.date_new').datetimepicker({
            timepicker: false,
            defaultDate: date,
            format: 'd/m/Y',
            minDate: startdate,
            maxDate: newdate,
        })
        validate_form();
        <?php if (empty($suggestOvertime)){ ?>
        $('#tb-payment-methods tbody').html('');
        if (allDate.length > 0){
            $.each(allDate,function (k,v){
                // addPaymentMethods(v);
            })
        }
        <?php } ?>
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
                    $('#suggest_overtime').modal('hide');
                } else {
                    alert_float('danger', data.message);
                }
            })
            .fail(function () {
                alert_float('danger', lang_core['errors']);
            });
        return false;
    }

    function checkExists(){
        var staff_id = $("#staff_id").val();
        var month = $("#month").val();
        var year = $("#year").val();
        id = 0;
        <?php if (!empty($suggestOvertime)){ ?>
        id = "<?= $suggestOvertime['id'] ?>";
        <?php } ?>
        if (staff_id && month && year) {
            dataString = {
                staff_id: staff_id,
                month: month,
                year: year,
                id: id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>suggest_overtime/checkExistsSuggestOvertime",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result){
                        var r = confirm(`Phiếu đề xuất tăng ca tháng ${month}  năm ${year} của nhân viên này đã tồn tại. Bạn có muốn sửa không ?`);
                        if (r == false) {
                            $("#staff_id").val('').selectpicker('refresh');
                            return false;
                        } else {
                            $('#suggest_overtime').modal('hide');
                            setTimeout(function (){
                                edit(response.id);
                            },400)
                        }
                    }
                }
            });
        }


        if (month && year) {
            dataString = {
                month: month,
                year: year,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>suggest_overtime/getDate",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result){
                        var date = new Date(`${year}-${month}-01`);
                        date.setDate(date.getDate());
                        var startdate = response.startdate;
                        var newdate = response.newdate;
                        $('.date_new').datetimepicker({
                            timepicker: false,
                            defaultDate: date,
                            autoclose: true,
                            format: 'd/m/Y',
                            minDate: startdate,
                            maxDate: newdate
                        });
                        $('#tb-payment-methods tbody').html('');
                        if (response.allDate.length > 0){
                            $.each(response.allDate,function (k,v){
                                // addPaymentMethods(v);
                            })
                        }
                    }
                }
            });
            return false;
        }
    }

    function changdateNew(_this){
        date_check = $(_this).val();
        if (date_check != '') {
            if (jQuery.inArray(date_check, arrId) !== -1) {
                alert_float('danger', 'Ngày này đã tồn tại');
                $(_this).val('');
                getTotal();
                return;
            }
            addPaymentMethods(date_check);
        }
        $(_this).val('');
        console.log('a');
    }

    function changeYear(_this){
        $('#tb-payment-methods tbody').html('');
        checkExists();
    }

    function changeMonth(_this){
        $('#tb-payment-methods tbody').html('');
        checkExists();
    }

    function changePersonel(_this) {
        personel_id = $(_this).val();
        checkExists();
    }

    function addPaymentMethods(date = '') {
        nLength = $('#tb-payment-methods tbody tr').length;

        var tdNumberPM = `<td class="stt text-center"></td>`;
        var tdDate = `<td>
             <div class="html_date"></div>
            <input type="hidden" name="pm[${counterPM}][date]" autocomplete="off"  class="form-control date_new_vs1 datepicker" value="${date}">
        </td>`;
        var tdHourOvertime = `<td class="text-center">
            <input type="text" onchange="getTotal();changeHourOvertime(this)" name="pm[${counterPM}][hour_overtime]"  class="form-control hour_overtime number-format" value="3">
        </td>`;
        var tdNote = `<td style="width: 120px" class="text-left">
             <textarea class="form-control note" name="pm[${counterPM}][note]" cols="2" rows="3"></textarea>
        </td>`;
        var tdActionsPM = `<td class="text-center"><span class="fa fa-remove text-danger pointer" onclick="removePaymentMethods(this)"></span></td>`;

        var trPM = `<tr>
            ${tdNumberPM}
            ${tdDate}
            ${tdHourOvertime}
            ${tdNote}
            ${tdActionsPM}
        </tr>`;

        $('#tb-payment-methods').append(trPM);
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
        arrId = [];
        for (iPM = 0; iPM < nPM; iPM++) {
            sttPM++;
            elementPM = $(tbPM)[iPM];
            $(elementPM).find('.stt').html(sttPM);
            date = $(elementPM).find('.date_new_vs1').val();
            $(elementPM).find('.html_date').html(date);
            if (date != '') {
                index = jQuery.inArray(date, arrId);
                if (index !== -1) {
                } else {
                    arrId.push(date);
                }
            }
        }
    }

    function removePaymentMethods(_this) {
        $(_this).closest('tr').remove();
        getTotal();
    }

    function changeHourOvertime(_this){;
        if ($(_this).val() > 15){
            $(_this).val(15);
        }
        if ($(_this).val() <= 0){
            $(_this).val(0.5);
        }
    }

</script>
