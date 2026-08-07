<style>
    #tb-payment-methods > thead > tr > th {
        border-top: 1px solid #93b4d6 !important;
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 150px !important;
    }
</style>
<div class="modal fade" id="business_fee_boiler" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <?php
            echo form_open_multipart(admin_url('business_fee_other/add_business_fee_boiler/' . $id),
                array('id' => 'business-fee-boiler-form'));
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
                            <?php $valueName = !empty($businessFeeBoiler) ? $businessFeeBoiler['name'] : '' ?>
                            <label for="name" class="control-label bold"><?= lang('Tên phiếu') ?></label>
                            <input type="text" class="form-control name" name="name" id="name"
                                   value="<?= $valueName ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_id" class="control-label bold"><?= lang('Nhân viên') ?></label>
                            <?php $arrSelect[] = !empty($businessFeeBoiler) ? $businessFeeBoiler['staff_id'] : '' ?>
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
                            <?php $arrSelect = [];
                            $arrSelect[] = !empty($businessFeeBoiler) ? $businessFeeBoiler['month'] : '' ?>
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
                            <?php $arrSelect = [];
                            $arrSelect[] = !empty($businessFeeBoiler) ? $businessFeeBoiler['year'] : ''; ?>
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
                        <table class="table table-hover" style="margin-top: 10px" id="tb-payment-methods">
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
                                <th class="text-center" style="width: 120px;"><?= lang('Ngày') ?></th>
                                <th class="text-center" style="width: 80px;"><?= lang('Km Tổng') ?></th>
                                <th class="text-center" style="width: 120px;"><?= lang('Chi tiết đoạn đường') ?></th>
                                <th class="text-center" style="width: 230px;"><?= lang('Tên công ty công tác') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Nhân viên đi cùng') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Lý do') ?></th>
                                <th class="text-center" style="width: 30px;"><span class="fa fa-trash-o"></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $counterPM = 0;
                            if (!empty($businessFeeBoilerDetail)) { ?>
                                <?php foreach ($businessFeeBoilerDetail as $key => $value) { ?>
                                    <?php
                                    $arrSelect = [];
                                    $businessFeeBoilerDetailStaff = get_table_where('tbl_business_fee_boiler_detail_staff',
                                        ['business_fee_boiler_detail_id' => $value['id']]);
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

                                    $this->db->select('tbl_business_fee_boiler_detail_customer.customer_id as customer_id,tblclients.company as company,total_km');
                                    $this->db->from('tbl_business_fee_boiler_detail_customer');
                                    $this->db->join('tblclients',
                                        'tblclients.userid = tbl_business_fee_boiler_detail_customer.customer_id');
                                    $this->db->where('business_fee_boiler_detail_id', $value['id']);
                                    $businessFeeBoilerDetailCustomer = $this->db->get()->result_array();
                                    ?>
                                    <tr>
                                        <td class="text-center stt"></td>
                                        <td>
                                            <input type="hidden" name="pm[<?= $counterPM ?>][conter]" class="conter"
                                                   value="<?= $counterPM ?>">
                                            <input type="hidden" name="pm[<?= $counterPM ?>][id]"
                                                   value="<?= $value['id'] ?>">
                                            <input type="text" required onchange="getTotal()"
                                                   name="pm[<?= $counterPM ?>][date]" autocomplete="off"
                                                   class="form-control date_new datepicker"
                                                   value="<?= _dhau($value['date']) ?>">
                                        </td>
                                        <td>
                                            <input type="text" required onchange="getTotal()"
                                                   name="pm[<?= $counterPM ?>][total_km]"
                                                   class="form-control total_km number-format"
                                                   value="<?= formatNumber($value['total_km']) ?>">
                                        </td>
                                        <td>
                                            <textarea class="form-control distance_detail"
                                                      name="pm[<?= $counterPM ?>][distance_detail]" cols="2"
                                                      rows="3"><?= $value['distance_detail'] ?></textarea>
                                        </td>
                                        <td class="td-customer">
                                            <div class="sub">
                                                <?php if (!empty($businessFeeBoilerDetailCustomer)) { ?>
                                                    <?php foreach ($businessFeeBoilerDetailCustomer as $kk => $vv) { ?>
                                                        <div class="sb">
                                                            <div class="col-md-8" style="padding: 0px;">
                                                                <select name="rel_id[<?= $counterPM ?>][]" id="rel_id"
                                                                        class="ajax-sesarch rel_id" data-width="100%"
                                                                        data-live-search="true"
                                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                    <?php
                                                                    echo '<option value="' . $vv['customer_id'] . '"  selected>' . $vv['company'] . '</option>';
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3" style="padding: 0px;"><input
                                                                        type="text" style="width: 100%;"
                                                                        name="quantity_sub[<?= $counterPM ?>][]"
                                                                        id="input" onchange="getTotal()"
                                                                        class="form-control quantity_sub number-format "
                                                                        value="<?= formatNumber($vv['total_km']) ?>"
                                                                        title=""></div>
                                                            <div class="col-md-1" style="padding: 0px;">
                                                                <div style="margin: 50%;"><i
                                                                            class="fa fa-remove remove-sub pointer text-danger"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            <div class="row col-md-12"><a class="pointer"
                                                                          onclick="addRowShipping(<?= $counterPM ?>, this)"><i
                                                            class="fa fa-plus"></i> Thêm công ty</a></div>
                                            <div class="text-danger show-errors"></div>
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
                                <td class="bold total_km_new text-center"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tfoot>
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
    var dtStaff = <?= !empty($staffNew) ? json_encode($staffNew) : '{}' ?>;

    <?php if (!empty($businessFeeBoiler)) { ?>
    $("#staff_id").trigger('change');
    task_rel_select();
    getTotal();
    <?php } ?>
    <?php if (empty($businessFeeBoiler)){ ?>
    addPaymentMethods();
    <?php } ?>
    function validate_form() {
        _validate_form($('#business-fee-boiler-form'), {
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
                    $('#business_fee_boiler').modal('hide');
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
        <?php if (!empty($businessFeeBoiler)){ ?>
        id = "<?= $businessFeeBoiler['id'] ?>";
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
                url: "<?= admin_url() ?>business_fee_other/checkExists",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result){
                        var r = confirm(`Phiếu theo dõi km tháng ${month}  năm ${year} của nhân viên này đã tồn tại. Bạn có muốn sửa không ?`);
                        if (r == false) {
                            $("#staff_id").val('').selectpicker('refresh');
                            return false;
                        } else {
                            $('#business_fee_boiler').modal('hide');
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


    function addPaymentMethods() {
        nLength = $('#tb-payment-methods tbody tr').length;

        var tdNumberPM = `<td class="stt text-center"></td>`;
        var tdDate = `<td>
            <input type="text" required onchange="getTotal()" name="pm[${counterPM}][date]" autocomplete="off"  class="form-control date_new datepicker" value="">
        </td>`;
        var tdKm = `<td>
           <input type="text" required onchange="getTotal()" name="pm[${counterPM}][total_km]"  class="form-control total_km number-format" value="0">
        </td>`;
        var tdDistanceDetail = `<td>
            <textarea class="form-control distance_detail" name="pm[${counterPM}][distance_detail]" cols="2" rows="3"></textarea>
        </td>`;
        var tdCompany = `<td class="text-left td-customer">
            <div class="sub"></div>
            <div class="row col-md-12"><a class="pointer" onclick="addRowShipping(${counterPM}, this)"><i class="fa fa-plus"></i> Thêm công ty</a></div>
            <div class="text-danger show-errors"></div>
        </td>`;
        var tdStaff = `<td style="width: 150px" class="text-center">
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
            ${tdKm}
            ${tdDistanceDetail}
            ${tdCompany}
            ${tdStaff}
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
        total_km = 0;
        for (iPM = 0; iPM < nPM; iPM++) {
            sttPM++;
            elementPM = $(tbPM)[iPM];
            $(elementPM).find('.stt').html(sttPM);
            quantity_total = intVal($(elementPM).find('.total_km').val());
            total_km += quantity_total;
            quantity_sub = 0;
            $.each($(elementPM).find('.quantity_sub'), function (index, el) {
                quantity_sub += intVal($(el).val());
            });
            if (quantity_sub > quantity_total) {
                alert_float('danger', 'Không được vượt quá số Km tổng');
                $(elementPM).find('.quantity_sub').val(0);
            }
        }
        $(".total_km_new").html(tnhFormatNumber(total_km))
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
        checkExisitTotal(value, _this);

    }

    function changeDateWork(_this) {
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');
    }

    function changeType(_this) {
        $("#tb-payment-methods tbody").find('tr').removeClass('newVs1');
    }

    function removePaymentMethods(_this) {
        $(_this).closest('tr').remove();
        getTotal();
    }


</script>
