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

</style>
<div class="modal fade" id="business_fee_boiler_overtime" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <?php
            echo form_open_multipart(admin_url('business_fee_other/add_business_fee_boiler_overtime/' . $id),
                array('id' => 'business-fee-boiler-overtime-form'));
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?= $title ?> </span>
                </h4>
            </div>
            <div class="modal-body" style="height:auto">
                <div class="table-responsive">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php $valueName = !empty($businessFeeBoilerOvertime) ? $businessFeeBoilerOvertime['name'] : '' ?>
                            <label for="name" class="control-label bold"><?= lang('Tên phiếu') ?></label>
                            <input type="text" class="form-control name" name="name" id="name"
                                   value="<?= $valueName ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_id" class="control-label bold"><?= lang('Nhân viên') ?></label>
                            <?php $arrSelect[] = !empty($businessFeeBoilerOvertime) ? $businessFeeBoilerOvertime['staff_id'] : '' ?>
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
                            <?php $arrSelect = []; $arrSelect[] = !empty($businessFeeBoilerOvertime) ? $businessFeeBoilerOvertime['month'] : '' ?>
                            <label for="month" class="control-label bold"><?= lang('Tháng') ?></label>
                            <select class="selectpicker month form-control" name="month" id="month"
                                    data-live-search="true"
                                    onchange="changeMonth(this)"
                                    title='<?php echo _l('Tháng'); ?>'
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            >
                                <?php foreach (getMonth() as $k => $v) : ?>
                                    <option <?= (!empty($arrSelect) && in_array($v,
                                            $arrSelect)) ? 'selected' : ($k == date('m') ? 'selected' : '') ?>
                                            value="<?= $k ?>"><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php $arrSelect = []; $arrSelect[] = !empty($businessFeeBoilerOvertime) ? $businessFeeBoilerOvertime['year'] : ''; ?>
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
                    <div class="col-md-12">
                        <div style="color: green;margin-top: 10px;font-size: 16px">Chi tiết theo ngày</div>
                        <div class="table-responsive-vs1">
                            <table class="table table-hover" style="margin-top: 10px;width: 1550px; min-height: 300px;max-width: unset"
                                   id="tb-payment-methods">
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
                                    <th class="text-center" style="width: 150px;"><?= lang('Ngày') ?></th>
                                    <th class="text-center"
                                        style="width: 200px;"><?= lang('Khách hàng-Đại điểm') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Từ giờ') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Đến giờ') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Ngày thường') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Chủ nhật') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Lễ tết') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('PC khảo sát') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Công tác tỉnh') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Nhân viên đi cùng') ?></th>
                                    <th class="text-center" style="width: 150px;"><?= lang('Lý do') ?></th>
                                    <th class="text-center" style="width: 30px;"><span class="fa fa-trash-o"></span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counterPM = 0;
                                if (!empty($businessFeeBoilerDetailOvertime)) { ?>
                                    <?php foreach ($businessFeeBoilerDetailOvertime as $key => $value) { ?>
                                        <?php
                                        $arrSelect = [];
                                        $businessFeeBoilerDetailStaff = get_table_where('tbl_business_fee_boiler_overtime_detail_staff',
                                            ['business_fee_boiler_overtime_detail_id' => $value['id']]);
                                        if (!empty($businessFeeBoilerDetailStaff)) {
                                            foreach ($businessFeeBoilerDetailStaff as $kk => $vv) {
                                                $arrSelect[] = $vv['staff_id'];
                                            }
                                        }
                                        $htmlStaff = '';
                                        foreach ($staffNew as $kk => $val) {
                                            $htmlStaff .= '<option ' . ((!empty($arrSelect) && in_array($val['id'],
                                                        $arrSelect)) ? 'selected' : '') . ' data-subtext="' . $val['name_department'] . '" value="' . $val['id'] . '">' . $val['fullname'] . '</option>';
                                        }
                                        $customer = get_table_where('tblclients',['userid' => $value['customer_id']],'','row_array');
                                        ?>
                                        <tr>
                                            <td class="text-center stt"></td>
                                            <td>
                                                <input type="hidden" name="pm[<?= $counterPM ?>][conter]" class="conter"
                                                       value="<?= $counterPM ?>">
                                                <input type="hidden" name="pm[<?= $counterPM ?>][id]"
                                                       value="<?= $value['id'] ?>">
                                                <input type="text" required onchange="getTotal()"
                                                       name="pm[<?= $counterPM ?>][date]"
                                                       autocomplete="off"
                                                       class="form-control date_new datepicker"
                                                       value="<?= _dhau($value['date']) ?>">
                                            </td>
                                            <td>
                                                <div class="flex-center">
                                                    <div class="radio radio-primary mright10">
                                                        <input type="radio" onchange="changeType(this)" name="pm[<?= $counterPM ?>][type]" id="type-1<?= $counterPM ?>" value="1" <?= $value['type'] == 1 ? 'checked' : '' ?>>
                                                        <label for="type-1<?= $counterPM ?>">Khách hàng</label>
                                                    </div>
                                                    <div class="radio radio-primary" style="margin-top: 10px;">
                                                        <input type="radio" onchange="changeType(this)" name="pm[<?= $counterPM ?>][type]" id="type-2<?= $counterPM ?>" value="2" <?= $value['type'] == 2 ? 'checked' : '' ?>>
                                                        <label for="type-2<?= $counterPM ?>">Địa điểm công ty</label>
                                                    </div>
                                                </div>
                                                <div class="wapper-customer <?= $value['type'] == 1 ? '' : 'hide' ?>">
                                                    <select <?= $value['type'] == 1 ? 'required' : '' ?>  name="pm[<?= $counterPM ?>][rel_id]" id="rel_id" class="ajax-sesarch rel_id" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php
                                                        echo '<option value="' . $value['customer_id'] . '"  selected>' . $customer['company'] . '</option>';
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="wapper-customer-text <?= $value['type'] == 2 ? '' : 'hide' ?>">
                                                    <input type="text" <?= $value['type'] == 2 ? 'required' : '' ?> name="pm[<?= $counterPM ?>][customer_text]"  class="form-control customer_text " value="<?= $value['customer_text'] ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" required onchange="getTotal()" name="pm[<?= $counterPM ?>][hour_start]"  class="form-control hour_start number-format" value="<?= $value['hour_start'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" required onchange="getTotal()" name="pm[<?= $counterPM ?>][hour_end]"  class="form-control hour_end number-format" value="<?= $value['hour_end'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" onchange="getTotal()" name="pm[<?= $counterPM ?>][weekday]"  class="form-control weekday number-format" value="<?= $value['weekday'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" onchange="getTotal()" name="pm[<?= $counterPM ?>][sunday]"  class="form-control sunday number-format" value="<?= $value['sunday'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" onchange="getTotal()" name="pm[<?= $counterPM ?>][holiday]"  class="form-control holiday number-format" value="<?= $value['holiday'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" onchange="getTotal()" name="pm[<?= $counterPM ?>][allowance_survey]"  class="form-control allowance_survey number-format" value="<?= $value['allowance_survey'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" onchange="getTotal()" name="pm[<?= $counterPM ?>][construction_allowance_province]"  class="form-control construction_allowance_province number-format" value="<?= $value['construction_allowance_province'] ?>">
                                            </td>
                                            <td>
                                                <select class="staff_id modal-select2 selectpicker"
                                                        data-live-search="true"
                                                        multiple
                                                        onchange="getTotal();"
                                                        title='<?php echo _l('nhân viên'); ?>'
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                        style="width: 100%;height: 30px"
                                                        name="pm[<?= $counterPM ?>][staff_id][]"
                                                        id="staff_id<?= $counterPM ?>">
                                                    <?= $htmlStaff ?>
                                                </select>
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
                                <tfoot>
                                <tr>
                                    <td colspan="2" class="uppercase">Tổng cộng</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="bold text-center total-weekday"></td>
                                    <td class="bold text-center total-sunday"></td>
                                    <td class="bold text-center total-holiday"></td>
<!--                                    <td class="bold text-center total-to"></td>-->
<!--                                    <td class="bold text-center total-go"></td>-->
                                    <td class="bold text-center total-vs1"></td>
                                    <td class="bold text-center total-vs2"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
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
    var dtStaff = <?= !empty($staffNew) ? json_encode($staffNew) : '{}' ?>;

    <?php if (!empty($businessFeeBoilerOvertime)) { ?>
    $("#staff_id").trigger('change');
    task_rel_select();
    getTotal();
    <?php } ?>
    <?php if (empty($businessFeeBoilerOvertime)){ ?>
    addPaymentMethods();
    <?php } ?>
    function validate_form() {
        _validate_form($('#business-fee-boiler-overtime-form'), {
            staff_id: "required",
            name: "required",
            year: "required",
            month: "required",
        }, add_payment);
    }

    var startdate = "<?= date('Y-m-01') ?>";
    var newdate = "<?= date("Y-m-t") ?>";
    $('.date_new').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        minDate: startdate,
        maxDate: newdate
    })


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
                    $('#business_fee_boiler_overtime').modal('hide');
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
        <?php if (!empty($businessFeeBoilerOvertime)){ ?>
        id = "<?= $businessFeeBoilerOvertime['id'] ?>";
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
                url: "<?= admin_url() ?>business_fee_other/checkExistsOvertime",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result){
                        var r = confirm(`Phiếu tăng ca tháng ${month}  năm ${year} của nhân viên này đã tồn tại. Bạn có muốn sửa không ?`);
                        if (r == false) {
                            $("#staff_id").val('').selectpicker('refresh');
                            return false;
                        } else {
                            $('#business_fee_boiler_overtime').modal('hide');
                            setTimeout(function (){
                                edit(response.id,response.status);
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
                url: "<?= admin_url() ?>business_fee_other/getDate",
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
                    }
                }
            });
            return false;
        }
    }

    function changeYear(_this){
        checkExists();
    }

    function changeMonth(_this){
        checkExists();
    }

    function changePersonel(_this) {
        personel_id = $(_this).val();

        checkExists();

        return ;
        $(".name_deparment").html('');
        $(".name_address").html('');
        $(".name_phone_number").html('');
        if (personel_id) {
            dataString = {
                personel_id: personel_id,
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
                }
            });
            return false;
        }
    }

    function addRowShipping(counter, _this) {
        cTr = $(_this).closest('tr');
        var div = $(_this).closest('.td-customer');

        html = `<div class="sb">
            <div class="col-md-8" style="padding: 0px;">
                 <select name="rel_id[${counter}][]" id="rel_id" class="ajax-sesarch rel_id" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                </select>
            </div>
            <div class="col-md-3" style="padding: 0px;"><input type="text" style="width: 100%;" name="quantity_sub[${counter}][]" id="input" onchange="getTotal()" class="form-control quantity_sub number-format " value="0" title=""></div>
            <div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>
            </div>`;
        div.find('.sub').append(html);
        getTotal();
        init_datepicker();
        task_rel_select();
    }

    $(document).on('click', '.remove-sub', function (event) {
        event.preventDefault();
        $(this).closest('.sb').remove();
        getTotal();
    });

    function task_rel_select() {
        var serverData = {};
        serverData.rel_id = $(".rel_id").val();
        serverData.type = 'customer';
        init_ajax_search('customer', $(".rel_id"), serverData);
    }

    function getStaff(select_id) {
        var option = '<option value=""></option>';
        $.each(dtStaff, function (index, el) {
            selected = select_id == el.id ? 'selected' : '';
            name_department = el.name_department != null ? el.name_department : '';
            option += `<option value="${el.id}" data-subtext="${name_department}">${el.fullname}</option>`;
        });
        return option;
    }

    function task_rel_select() {
        var serverData = {};
        serverData.rel_id = $(".rel_id").val();
        serverData.type = 'customer';
        init_ajax_search('customer', $(".rel_id"), serverData);
    }

    function changeType(_this){
        value = $(_this).val();
        console.log(value)
        if (value == 1){
            $(_this).closest('tr').find('.wapper-customer').removeClass('hide');
            $(_this).closest('tr').find('.wapper-customer-text').addClass('hide');
            $(_this).closest('tr').find('.rel_id').attr('required',true);
            $(_this).closest('tr').find('.customer_text').attr('required',false);
            $(_this).closest('tr').find('.customer_text').val('');
        } else {
            $(_this).closest('tr').find('.wapper-customer').addClass('hide');
            $(_this).closest('tr').find('.wapper-customer-text').removeClass('hide');
            $(_this).closest('tr').find('.rel_id').attr('required',false);
            $(_this).closest('tr').find('.customer_text').attr('required',true);
            $(_this).closest('tr').find('.rel_id').val('').selectpicker('refresh');
        }
    }

    function addPaymentMethods() {
        nLength = $('#tb-payment-methods tbody tr').length;

        var tdNumberPM = `<td class="stt text-center"></td>`;
        var tdDate = `<td>
            <input type="text" required onchange="getTotal()" name="pm[${counterPM}][date]" autocomplete="off"  class="form-control date_new datepicker" value="">
        </td>`;
        var tdCompany = `<td>
                <div class="flex-center">
                    <div class="radio radio-primary mright10">
                        <input type="radio" onchange="changeType(this)" name="pm[${counterPM}][type]" id="type-1${counterPM}" value="1" checked>
                        <label for="type-1${counterPM}">Khách hàng</label>
                    </div>
                    <div class="radio radio-primary" style="margin-top: 10px;">
                        <input type="radio" onchange="changeType(this)" name="pm[${counterPM}][type]" id="type-2${counterPM}" value="2">
                        <label for="type-2${counterPM}">Địa điểm công ty</label>
                    </div>
                </div>
             <div class="wapper-customer">
                <select required name="pm[${counterPM}][rel_id]" id="rel_id" class="ajax-sesarch rel_id" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                </select>
            </div>
            <div class="wapper-customer-text hide">
                <input type="text" name="pm[${counterPM}][customer_text]"  class="form-control customer_text " value="">
            </div>
        </td>`;
        var tdHourStart = `<td>
            <input type="text" required onchange="getTotal()" name="pm[${counterPM}][hour_start]"  class="form-control hour_start number-format" value="">
        </td>`;
        var tdHourEnd = `<td>
            <input type="text" required onchange="getTotal()" name="pm[${counterPM}][hour_end]"  class="form-control hour_end number-format" value="">
        </td>`;
        var tdWeekday = `<td class="text-center">
            <input type="text" onchange="getTotal()" name="pm[${counterPM}][weekday]"  class="form-control weekday number-format" value="0">
        </td>`;
        var tdSunday = `<td class="text-center">
            <input type="text" onchange="getTotal()" name="pm[${counterPM}][sunday]"  class="form-control sunday number-format" value="0">
        </td>`;
        var tdHoliday = `<td class="text-center">
            <input type="text" onchange="getTotal()" name="pm[${counterPM}][holiday]"  class="form-control holiday number-format" value="0">
        </td>`;
        var tdGoNight = `<td class="text-center">
            <input type="text" onchange="getTotal()" name="pm[${counterPM}][go_night]"  class="form-control go_night number-format" value="0">
        </td>`;
        var tdBackNight = `<td class="text-center">
            <input type="text" onchange="getTotal()" name="pm[${counterPM}][back_night]"  class="form-control back_night number-format" value="0">
        </td>`;
        var tdAllowance = `<td class="text-center">
            <input type="text" onchange="getTotal()" name="pm[${counterPM}][allowance_survey]"  class="form-control allowance_survey number-format" value="0">
        </td>`;
        var tdAllowanceVs1 = `<td class="text-center">
            <input type="text" onchange="getTotal()" name="pm[${counterPM}][construction_allowance_province]"  class="form-control construction_allowance_province number-format" value="0">
        </td>`;
        var tdStaff = `<td style="width: 200px" class="text-center">
            <input type="hidden" name="pm[${counterPM}][conter]" class="conter" value="${counterPM}">
            <select class="staff_id modal-select2 selectpicker"
                data-live-search="true"
                multiple
                onchange="getTotal();"
                title='<?php echo _l('Nhân viên đi cùng'); ?>'
                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                style="width: 100%;height: 30px" name="pm[${counterPM}][staff_id][]" id="staff_id${counterPM}">
                ${getStaff()}
            </select>
        </td>`;
        var tdNote = `<td style="width: 120px" class="text-left">
             <textarea class="form-control note" name="pm[${counterPM}][note]" cols="2" rows="3"></textarea>
        </td>`;
        var tdActionsPM = `<td class="text-center"><span class="fa fa-remove text-danger pointer" onclick="removePaymentMethods(this)"></span></td>`;

        var trPM = `<tr>
            ${tdNumberPM}
            ${tdDate}
            ${tdCompany}
            ${tdHourStart}
            ${tdHourEnd}
            ${tdWeekday}
            ${tdSunday}
            ${tdHoliday}
            ${tdAllowance}
            ${tdAllowanceVs1}
            ${tdStaff}
            ${tdNote}
            ${tdActionsPM}
        </tr>`;

        $('#tb-payment-methods').append(trPM);
        getTotal();
        init_datepicker();
        init_selectpicker();
        task_rel_select();
        counterPM++;
    }

    function getTotal() {
        var tbPM = '#tb-payment-methods tbody tr:not("[class^=not-tr]")';
        var nPM = $(tbPM).length;
        var sttPM = 0;
        countError = 0;
        total_weekday = 0;
        total_sunday = 0;
        total_holiday = 0;
        total_to = 0;
        total_go = 0;
        total_vs1 = 0;
        total_vs2 = 0;
        for (iPM = 0; iPM < nPM; iPM++) {
            sttPM++;
            elementPM = $(tbPM)[iPM];
            $(elementPM).find('.stt').html(sttPM);
            weekday = intVal($(elementPM).find('.weekday').val());
            sunday = intVal($(elementPM).find('.sunday').val());
            holiday = intVal($(elementPM).find('.holiday').val());
            go_night = intVal($(elementPM).find('.go_night').val());
            back_night = intVal($(elementPM).find('.back_night').val());
            construction_allowance = intVal($(elementPM).find('.construction_allowance').val());
            construction_allowance_province = intVal($(elementPM).find('.construction_allowance_province').val());

            total_weekday += weekday;
            total_sunday += sunday;
            total_holiday += holiday;
            total_to += go_night;
            total_go += back_night;
            total_vs1 += construction_allowance;
            total_vs2 += construction_allowance_province;
        }
        $(".total-weekday").html(tnhFormatNumber(total_weekday));
        $(".total-sunday").html(tnhFormatNumber(total_sunday));
        $(".total-holiday").html(tnhFormatNumber(total_holiday));
        $(".total-to").html(tnhFormatNumber(total_to));
        $(".total-go").html(tnhFormatNumber(total_go));
        $(".total-vs1").html(tnhFormatNumber(total_vs1));
        $(".total-vs2").html(tnhFormatNumber(total_vs2));
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


    function changeDate(_this) {
        value = $(_this).val();
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');
        $(_this).closest('tr').addClass('current newVs1');
        $(_this).closest('tr').attr('data-date', value);

    }

    function changeDateWork(_this) {
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');
    }


    function removePaymentMethods(_this) {
        $(_this).closest('tr').remove();
        getTotal();
    }


</script>
