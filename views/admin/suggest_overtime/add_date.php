<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    #tnhModal2 {
        z-index: 10002;
    }
    #tb-payment-methods1 > thead > tr > th {
        border-top: 1px solid #93b4d6 !important;
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }
</style>
<?php
echo form_open_multipart(admin_url('suggest_overtime/add_date_post/' . $id),
    array('id' => 'form-add-date'));
?>
<div class="modal-dialog modal-lg" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Thêm ngày vào phiếu đề xuất tăng ca') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div>Tên phiếu : <?= $dtSuggest['name'] ?></div>
                    <div>Tháng/ Năm : <?= $dtSuggest['month'] .'/ '.$dtSuggest['year'] ?></div>
                    <div>Nhân viên : <?= get_staff_full_name($dtSuggest['staff_id']) ?></div>
                </div>
                <div class="col-md-12">
                    <label for="date_new" class="control-label bold"><?= lang('Chọn ngày') ?></label>
                    <input type="text" onchange="changdate(this)" class="form-control date_new datepicker" id="date_new">
                </div>
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table class="table table-hover" style="margin-top: 10px;width: 100%"
                               id="tb-payment-methods1">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="hidden" name="id" id="id" class="form-control" value="<?= $dtSuggest['id'] ?>">
            <button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">

    var counterPM = <?= !empty($counterPM) ? $counterPM : 0 ?>;
    var countError = 0;

    var arrId = [];

    function validate_form() {
        _validate_form($('#form-add-date'), {
        }, add_payment);
    }
    $(function () {
        validate_form();
        var startdate = "<?= $startdate ?>";
        var newdate = "<?= $newdate ?>";
        var date = new Date(`${startdate}`);
        $('.date_new').datetimepicker({
            timepicker: false,
            format: 'd/m/Y',
            defaultDate: date,
            minDate: startdate,
            maxDate: newdate
        })
    });

    function changdate(_this){
        date = $(_this).val();
        if (date != '') {
            dataString = {
                date: date,
                id: $("#id").val(),
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>suggest_overtime/checkExistsDate",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result) {
                        alert_float('danger', response.message);
                        $(_this).val('');
                    } else {
                        if (jQuery.inArray(date, arrId) !== -1) {
                            alert_float('danger', 'Ngày này đã tồn tại');
                            $(_this).val('');
                            getTotal();
                            return;
                        }
                        addPaymentMethods(date);
                        $(_this).val('');
                    }
                }
            })
        }
    }

    function addPaymentMethods(date = '') {
        nLength = $('#tb-payment-methods1 tbody tr').length;

        var tdNumberPM = `<td class="stt text-center"></td>`;
        var tdDate = `<td>
             <div class="html_date"></div>
            <input type="hidden" name="pm[${counterPM}][date]" autocomplete="off"  class="form-control date_new_vs1 datepicker" value="${date}">
        </td>`;
        var tdHourOvertime = `<td class="text-center">
            <input type="text" onchange="getTotal();changeHourOvertime(this)" name="pm[${counterPM}][hour_overtime]"  class="form-control hour_overtime number-format" value="5">
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

        $('#tb-payment-methods1').append(trPM);
        getTotal();
        init_datepicker();
        init_selectpicker();
        counterPM++;
    }

    function getTotal() {
        var tbPM = '#tb-payment-methods1 tbody tr:not("[class^=not-tr]")';
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
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                }
            })
            .fail(function () {
                alert_float('danger', lang_core['errors']);
            });
        return false;
    }
</script>