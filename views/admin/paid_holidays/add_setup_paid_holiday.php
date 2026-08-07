<style>
    #tb-payment-methods-new > thead > tr > th {
        border-top: 1px solid #93b4d6 !important;
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }

    .content-bom {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }

    .table-responsive {
        max-height: 400px;
    }
</style>
<div class="modal fade" id="setup_paid_holiday" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <?php
            echo form_open_multipart(admin_url('paid_holidays/add_setup_paid_holiday/' . $id),
                array('id' => 'setup-paid-holiday-form'));
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="year" class="control-label bold"><?= lang('Năm') ?></label>
                            <?php $valueYear = !empty($paidholiday) ? $paidholiday['year'] : '' ?>
                            <select class="selectpicker year form-control" name="year" id="year"
                                    data-live-search="true"
                                    onchange="changeYear(this)"
                                    title='<?php echo _l('Năm'); ?>'
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php foreach (getYear() as $key => $value) { ?>
                                    <option <?= $valueYear == $key ? 'selected' : '' ?>
                                            value="<?= $key ?>"> <?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 hide_staff_search hide">
                        <div class="form-group">
                            <label for="staff_search" class="control-label"><?= lang('Tìm kiếm nhân viên') ?></label>
                            <input type="text" class="form-control staff_search" id="staff_search" placeholder="nhập nhân viên cần tìm kiếm">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-top: 10px" id="tb-payment-methods-new">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 30px;">
                                        <a class="hover-svg" onclick="addPaymentMethods()">
                                            <svg width="20" height="20" viewBox="0 0 30 30" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"/>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z"
                                                      fill="white"/>
                                            </svg>
                                        </a>
                                    </th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Nhân viên') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Phép năm') ?></th>
                                    <th class="text-center" style="width: 30px;"><span class="fa fa-trash-o"></span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counterPM = 0;
                                if (!empty($paidholidayDetai)) { ?>
                                    <?php foreach ($paidholidayDetai as $key => $value) { ?>
                                        <?php
                                        $htmlOption = '';
                                        if (!empty($staff)) {
                                            foreach ($staff as $kk => $vv) {
                                                $htmlOption .= '<option ' . ($vv['id'] == $value['staff_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '" data-subtext="' . $vv['name_department'] . '">' . $vv['fullname'] . '</option>';
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td class="stt text-center"></td>
                                            <td>
                                                <input type="hidden" name="pm[<?= $counterPM ?>][conter]"
                                                       value="<?= $counterPM ?>">
                                                <input type="hidden" name="pm[<?= $counterPM ?>][id]"
                                                       value="<?= $value['id'] ?>">
                                                <select class="staff_id modal-select2 selectpicker"
                                                        data-live-search="true"
                                                        title='<?php echo _l('Nhân viên'); ?>'
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                        style="width: 100%;height: 30px"
                                                        name="pm[<?= $counterPM ?>][staff_id]"
                                                        id="staff_id<?= $counterPM ?>">
                                                    <?= $htmlOption ?>
                                                </select>
                                                <div class="text_error_magic" style="color:red"></div>
                                            </td>
                                            <td>
                                                <input type="text" required onchange="getTotalNew()"
                                                       name="pm[<?= $counterPM ?>][number_day]"
                                                       class="form-control number_day number-format"
                                                       value="<?= $value['number_day'] ?>">
                                            </td>
                                            <td class="text-center">
                                                <span class="fa fa-remove text-danger pointer"
                                                      onclick="removePaymentMethods(this)"></span>
                                            </td>
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
    var dtStaff = <?= !empty($staff) ? json_encode($staff) : '{}' ?>;
    var arrIdNew = [];

    <?php if (!empty($paidholiday)) { ?>
    getTotalNew();
    <?php } ?>
    <?php if (empty($paidholiday)){ ?>
    // addPaymentMethods();
    <?php } ?>
    function validate_form() {
        _validate_form($('#setup-paid-holiday-form'), {
            year: "required",
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
                    $('#setup_paid_holiday').modal('hide');
                } else {
                    alert_float('danger', data.message);
                }
            })
            .fail(function () {
                alert_float('danger', lang_core['errors']);
            });
        return false;
    }

    function changeYear(_this) {
        year = $(_this).val();
        id = "<?= $id ?>";
        $("#tb-payment-methods-new tbody").html('');
        if (year) {
            dataString = {
                year: year,
                id: id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>paid_holidays/checkExistYear",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result) {
                        alert_float('danger', `Năm ${year} đã thiết lập ngày phép`);
                        $("#year").val('').selectpicker('refresh');
                    } else {
                        $.each(dtStaff, function (k, v) {
                            addPaymentMethods(v);
                        })
                    }
                }
            });
            return false;
        }
    }

    function getStaff(select_id) {
        var option = '<option value=""></option>';
        $.each(dtStaff, function (index, el) {
            selected = select_id == el.id ? 'selected' : '';
            name_department = el.name_department != null ? el.name_department : '';
            option += `<option ${selected} value="${el.id}" data-subtext="${name_department}">${el.fullname}</option>`;
        });
        return option;
    }

    function addPaymentMethods(value = {}) {
        nLength = $('#tb-payment-methods-new tbody tr').length;

        var tdNumberPM = `<td class="stt text-center"></td>`;
        var tdStaff = `<td>
            <input type="hidden" name="pm[${counterPM}][conter]" value="${counterPM}">
            <select class="staff_id modal-select2 selectpicker"
            data-live-search="true"
            title='<?php echo _l('Nhân viên'); ?>'
            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
            style="width: 100%;height: 30px" name="pm[${counterPM}][staff_id]" id="staff_id${counterPM}">
                ${getStaff(value.id)}
            </select>
            <div class="text_error_magic" style="color:red"></div>
        </td>`;
        var tdNumberDayPM = `<td class="text-left">
              <input type="text" required onchange="getTotalNew()" name="pm[${counterPM}][number_day]"  class="form-control number_day number-format" value="12">
        </td>`;
        var tdActionsPM = `<td class="text-center"><span class="fa fa-remove text-danger pointer" onclick="removePaymentMethods(this)"></span></td>`;

        var trPM = `<tr>
            ${tdNumberPM}
            ${tdStaff}
            ${tdNumberDayPM}
            ${tdActionsPM}
        </tr>`;

        $('#tb-payment-methods-new').append(trPM);
        $(`#staff_id${counterPM}`).attr('required', true);
        getTotalNew();
        init_datepicker();
        init_selectpicker();
        counterPM++;
    }


    function getTotalNew() {
        var tbPM = '#tb-payment-methods-new tbody tr:not("[class^=not-tr]")';
        var nPM = $(tbPM).length;
        var sttPM = 0;
        countError = 0;
        arrIdNew = [];
        if (nPM > 0){
            $(".hide_staff_search").removeClass('hide');
        } else {
            $(".hide_staff_search").addClass('hide');
        }
        for (iPM = 0; iPM < nPM; iPM++) {
            sttPM++;
            elementPM = $(tbPM)[iPM];
            $(elementPM).find('.stt').html(sttPM);
            staff_current_id = $(elementPM).find('select.staff_id').val();
            if (staff_current_id) {
                index = jQuery.inArray(staff_current_id, arrIdNew);
                if (index !== -1) {
                } else {
                    arrIdNew.push(staff_current_id);
                }
            }
        }
    }


    $(document).on('change', 'select.staff_id', function (
        event) {
        event.preventDefault();
        staff_id = $(this).val();
        if (staff_id) {
            if (jQuery.inArray(staff_id, arrIdNew) !== -1) {
                alert_float('danger', 'Nhân viên này đã tồn tại');
                $(this).val('').selectpicker('refresh');
                getTotalNew();
                return;
            }
        }
    });

    function removePaymentMethods(_this) {
        $(_this).closest('tr').remove();
        getTotalNew();
    }

    $(document).ready(function () {
        searchTableCustomNew('#tb-payment-methods-new', '#staff_search', '.tpagination');
    });

    function searchTableCustomNew(elTable, elSearch, elPanigation) {
        $(elSearch).keyup(function(event){
            var search_string = bodauTiengViet($.trim($(elSearch).val()).replace(/ +/g,' ').toLowerCase());
            if (search_string == '') {
                $(''+elTable+' tbody tr').attr('tsearch','ok');
                tpanigation(elTable, 1, 1);
            } else {
                var listRows = $(''+elTable+' tbody tr');
                $(listRows).attr('tsearch','notok');
                for(i = 0 ; i<listRows.length; i++)
                {
                    // var str = bodauTiengViet(listRows[i].innerHTML.toLowerCase());
                    var str = bodauTiengViet($(listRows[i].children[1]).find('select option:selected').html().toLowerCase());
                    if(str.search(search_string) >=0 )
                    {
                        $(listRows[i]).attr('tsearch','ok');
                    }
                }
                tpanigation(elTable, 1, 1);
            }
            createPanigation(elTable, elPanigation, 1);
        });
    }
</script>
