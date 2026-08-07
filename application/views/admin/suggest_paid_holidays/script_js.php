<script>
    ajaxSelectParams('#production_report_id', 'admin/suggest_repalce/searchProductionReports', $("#production_report_id").val(), true, true);
    $("#staff_id").select2();
    $("#staff_reciever").select2();
    $("#staff_agree").select2();

    function getTypeMagic(select_id) {
        var option = '<option value=""></option>';
        $.each(dtTypeMagic, function (index, el) {
            selected = select_id == el.id ? 'selected' : '';
            option += `<option ${selected} value="${el.id}">${el.name}</option>`;
        });
        return option;
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

    function optionResult(selected_id = 0){
        option = `<option></option>`;
        $.each(dtResult, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option+= '<option '+selected+' value="'+el.id+'">'+el.name+'</option>';
        });
        return option;
    }


    function addPaymentMethods(type_magic_new = 0, date_paid = '') {
        nLength = $('#tb-payment-methods tbody tr').length;
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');

        var tdNumberPM = `<td class="stt text-center"></td>`;
        var tdTypeMagic = `<td>
            <input type="hidden" name="conter[${counter}]" class="conter" value="${counter}">
            <select class="type_magic modal-select2 selectpicker"
             data-live-search="true"
             onchange="changeType(this);getTotal();"
            title='<?php echo _l('Loại phép'); ?>'
            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
            style="width: 100%;height: 30px" name="type_magic[${counter}]" id="type_magic${counter}">
                ${getTypeMagic(type_magic_new)}
            </select>
            <div class="text_error_magic" style="color:red"></div>
        </td>`;
        var tdDateStart = `<td>
            <div class="html_date_start"></div>
           <input type="hidden" required onchange="getTotal();changeDate(this);" name="date_start[${counter}]" autocomplete="off"  class="form-control date_start datepicker" value="${date_paid}">

        </td>`;
        var tdDateEnd = `<td>
            <div class="html_date_end"></div>
           <input type="hidden" required onchange="getTotal();changeDate(this);" name="date_end[${counter}]" autocomplete="off"  class="form-control date_end datepicker" value="${date_paid}">
        </td>`;
        var tdNumberDayPM = `<td class="text-left td-date">
              <input type="hidden" required onchange="getTotal()" name="number_day[${counter}]"  class="form-control number_day number-format" value="0">
             <div class="sub"></div>
        </td>`;
        var tdDayWork = `<td class="text-left ">
              <input type="text" required name="day_work[${counter}]" onchange="changeDateWork(this);getTotal();" autocomplete="off"  class="form-control day_work datepicker" value="">
        </td>`;
        var tdNote = `<td style="width: 120px" class="text-left">
             <textarea class="form-control note" name="note[${counter}]" cols="2" rows="2"></textarea>
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
        $(`#type_magic${counter}`).attr('required', true);
        getTotal();
        init_datepicker();
        init_selectpicker();
        counter++;
    }

    function removePaymentMethods(el)
    {
        $(el).closest('tr').remove();
        getTotal();
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
                    $(elementPM).find('.number_day').val(1);
                }
            } else {
                $(elementPM).find('.number_day').val(1);
            }
        }
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

    function addNumberDay(month_end, month_start, counter, _this) {
        diff = (month_end - month_start) > 0 ? (month_end - month_start) : 0;
        var div = $(_this).find('.td-date');
        html = '';
        if (diff >= 0) {
            for (i = month_end; i >= month_start; i--) {
                i_new = i != month_end ? '0' + i : i;
                html += `<div class="sb" style="display: flex;align-items: center">
                <div class="col-md-7" style="padding: 0px;"><span class="bold" style="font-style: italic">Tháng ${i_new}</span><input type="hidden" name="month_sub[${counter}][]" value="${i_new}" style="width: 100%;" title=""></div>
                <div class="col-md-5" style="padding: 0px;"><input type="text" onchange="getTotalNew()" required style="width: 100%;" name="quantity_sub[${counter}][]" id="input" class="form-control quantity_sub number-format" value="1" ></div>
                </div>`;
            }
        }
        div.find('.sub').html(html);
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


    function changeDateTable() {
        var items = $('table#tb-payment-methods tbody').find('tr:not(".edit_date")');
        $.each(items, (index, value) => {
            $(value).find('.date_start').trigger('change');
        });
    }

    appValidateForm($('#suggest_paid_holiday'), {
        reference_no: 'required',
        date: 'required',
        staff_id: 'required',
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
                console.log(data);
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = site.base_url+'admin/suggest_paid_holidays';
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
</script>