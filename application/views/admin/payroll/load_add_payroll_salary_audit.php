<table id="tb-payroll-salary" class="table dataTable tb-payroll-salary-new" style="width: 100%;">
    <thead>
    <?= $tHead ?>
    </thead>
    <tbody>
    <?= $html ?>
    </tbody>
    <tfoot>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th class="footer_salary_bhxh"></th>
        <th class="footer_salary_position"></th>
        <th class="footer_salary_responsibility"></th>
        <th class="footer_salary_all"></th>
        <th class="footer_concurrently_salary"></th>
        <th class="footer_sales_salary"></th>
        <th class="footer_business_fee_staff_salary"></th>
        <th class="footer_phone_salary"></th>
        <th class="footer_gasonline_cars_salary"></th>
        <th class="footer_motel_salary"></th>
        <th class="footer_total_phu_cap"></th>
        <th class="footer_seniority"></th>
        <th class="footer_day_number"></th>
        <th class="footer_day_number_new"></th>
        <th class="footer_day_number_holiday"></th>
        <th class="footer_day_number_lt"></th>
        <th class="footer_day_number_ch"></th>
        <th class="footer_day_number_other"></th>
        <th class="footer_day_number_od"></th>
        <th class="footer_total_number_day"></th>
        <th class="footer_salary_income"></th>
        <th class="footer_total_hour_1_5"></th>
        <th class="footer_total_hour_2_0"></th>
        <th class="footer_total_hour_3_0"></th>
        <th class="footer_total_hour_2_1"></th>
        <th class="footer_total_hour_2_7"></th>
        <th class="footer_allowance_business_fee"></th>
        <?php if (!empty($dtAllowance)) { ?>
            <?php foreach ($dtAllowance as $key => $value) { ?>
                <th class="footer_allowance_<?= $value['id'] ?>"></th>
            <?php } ?>
        <?php } ?>
        <th class="footer_allowance_rice"></th>
        <th class="footer_allowance_rice_money"></th>
        <th class="footer_allowance_rice_tc"></th>
        <th class="footer_total_salary_compensation"></th>
        <th class="footer_total_allowance"></th>
        <th class="footer_total"></th>
        <?php if (!empty($dtReduce)) { ?>
            <?php foreach ($dtReduce as $key => $value) { ?>
                <th class="footer_reduce_<?= $value['id'] ?>"></th>
            <?php } ?>
        <?php } ?>
        <th class="footer_deduct_advance"></th>
        <th class="footer_total_reduce"></th>
        <th class="footer_deduct_bhxh"></th>
        <th class="footer_deduct_bhyt"></th>
        <th class="footer_deduct_bhtn"></th>
        <th class="footer_deduct_union"></th>
        <th class="footer_total_reduce_bhxh"></th>
        <th class="footer_total_grand_total_kt"></th>
        <th class="footer_total_grand_total_kl"></th>
        <th class="footer_total_allowance_family"></th>
        <th class="footer_allowance_business_fee_new"></th>
        <th class="footer_total_tax_exemption"></th>
        <th class="footer_total_complete_permission"></th>
        <th class="footer_total_income_taxes"></th>
        <th class="footer_total_taxable_income"></th>
        <th class="footer_total_vat"></th>
        <th class="footer_total_real"></th>
    </tr>
    </tfoot>
</table>
<script>
    var dtHistoryJob;
    var rice_money_max = "<?= get_option('rice_money_max') ?>"
    var money_vat = "<?= get_option('money_vat') ?>"
    var money_reduce = "<?= get_option('money_reduce') ?>"
    var rice_money = "<?= get_option('rice_money_audit') ?>"
    var count_day_work = "<?= COUNT_DAY_WORK ?>"
    var hour_day = "<?= HOUR_DAY ?>"
    var coefficient_sunday = "<?= get_option('coefficient_sunday') ?>";
    var coefficient_holiday = "<?= get_option('coefficient_holiday') ?>";
    var coefficient_default = "<?= get_option('coefficient_default') ?>";
    var coefficient_default_night = "<?= get_option('coefficient_default_night') ?>";
    var coefficient_sunday_night = "<?= get_option('coefficient_sunday_night') ?>";
    var coefficient = "<?= get_option('coefficient') ?>";
    dtAllowance = <?= !empty($dtAllowance) ? json_encode($dtAllowance) : '{}' ?>;
    dtReduce = <?= !empty($dtReduce) ? json_encode($dtReduce) : '{}' ?>;
    $(document).ready(function () {
        $('.payrollPayment').selectpicker();
        dtHistoryJob = $('#tb-payroll-salary').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            fixedColumns: {
                leftColumns: 5,
                rightColumns: 0
            },
            scrollY: '430px',
            scrollX: true,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            "drawCallback": function (aoData, settings) {
                $('.payrollPayment').selectpicker();
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {

            },
            "initComplete": function (settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                totalSalary();
                mainWrapperHeightFix();
            },
            "footerCallback": function (nRow, aaData, start, end, display) {

            }
        });

    });

    function addPayrollPayment(_this) {
        cTr = $(_this).closest('tr');
        cTrChonse = cTr;
        cId = cTr.find('input.id').val();
        console.log(cId);
        cStaffId = cTr.find('input.staff_id').val();

        data_json = cTr.find('input.data_json_payment').val();
        cCounter = cTr.find('.counter').val();

        month = $("#month").val();
        year = $("#year").val();

        link = site.base_url + 'admin/payroll/callPayment';
        $.ajax({
            url: link,
            type: 'POST',
            dataType: 'html',
            data: {
                cId: cId,
                cStaffId: cStaffId,
                data_json: data_json,
                month: month,
                year: year,
                csrf_token_name: hash
            },
        })
            .done(function (data) {
                $('.modal-select2').select2('close');
                $('#tnhModal2').html(data);
            })
            .fail(function () {
                console.log("error");
            });
        $('#tnhModal2').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    $(".allowance_other_new").change(function () {
        totalSalary();
    });
    $(".reduce_other").change(function () {
        totalSalary();
    });
    $(".day_number").change(function () {
        totalSalary();
    });
    $(".day_number_off").change(function () {
        totalSalary();
    });
    $(".payrollPayment").change(function () {
        totalSalary();
    });
    $(".allowance_responsibility").change(function () {
        totalSalary();
    });
    $(".allowance_other").change(function () {
        totalSalary();
    });
    $(".allowance_manu").change(function () {
        totalSalary();
    });
    $(".allowance_western").change(function () {
        totalSalary();
    });
    $(".allowance_rice").change(function () {
        totalSalary();
    });
    $(".allowance_rice_tc").change(function () {
        totalSalary();
    });
    $(".bonus_holiday").change(function () {
        totalSalary();
    });
    $(".business_fee_difference").change(function () {
        totalSalary();
    });
    $('.check_hour').click(function () {
        totalSalary();
    });
    $('.hour_late').change(function () {
        totalSalary();
    });
    $('.free_salary').change(function () {
        totalSalary();
    });
    $('.salary_compensation').change(function () {
        totalSalary();
    });
    $('.tax_exemption').change(function () {
        totalSalary();
    });
    $('.complete_permission').change(function () {
        totalSalary();
    });
    $('.taxable_income').change(function () {
        totalSalary();
    });
    $('.total_weekday').change(function () {
        totalSalary();
    });
    $('.total_sunday').change(function () {
        totalSalary();
    });
    $('.total_holiday').change(function () {
        totalSalary();
    });
    $('.total_weekday_night').change(function () {
        totalSalary();
    });
    $('.total_sunday_night').change(function () {
        totalSalary();
    });

    function totalSalary() {
        tb = '#tb-payroll-salary tbody tr';
        var n = $(tb).length;
        count_error = 0;

        footer_salary_bhxh = 0;
        footer_allowance = 0;
        footer_total_salary_new = 0;
        footer_day_number = 0;
        footer_salary_income = 0;
        footer_allowance_responsibility = 0;
        footer_sales = 0;
        footer_phone = 0;
        footer_gasonline_cars = 0;
        footer_motel = 0;
        footer_concurrently = 0;
        footer_business_fee_staff = 0;
        footer_seniority = 0;
        footer_total_vat = 0;
        footer_allowance_other = 0;
        footer_allowance_manu = 0;
        footer_allowance_western = 0;
        footer_allowance_business_fee = 0;
        footer_allowance_business_fee_new = 0;
        footer_allowance_rice = 0;
        footer_allowance_rice_money = 0;
        footer_total_allowance = 0;
        footer_bonus_holiday = 0;
        footer_deduct_bhxh = 0;
        footer_deduct_bhyt = 0;
        footer_deduct_bhtn = 0;
        footer_deduct_union = 0;
        footer_deduct_advance = 0;
        footer_total = 0;
        footer_total_real = 0;
        footer_total_reduce = 0;
        footer_salary_responsibility = 0;
        footer_salary_position = 0;
        footer_responsibility_salary = 0;
        footer_salary_all = 0;
        footer_total_phu_cap = 0;
        footer_day_number_new = 0;
        footer_day_number_holiday = 0;
        footer_day_number_lt = 0;
        footer_day_number_ch = 0;
        footer_allowance_rice_tc = 0;

        footer_day_number_off = 0;
        footer_day_number_off_new = 0;
        footer_salary_off = 0;

        footer_hour_late = 0;
        footer_money_hour_late = 0;

        footer_total_reduce_bhxh = 0;

        footer_total_grand_total_kt = 0;
        footer_total_grand_total_kl = 0;

        footer_total_vat = 0;

        if (dtAllowance.length > 0) {
            $.each(dtAllowance, function (k, v) {
                id_check = v.id;
            });
        }

        for (ii = 0; ii < n; ii++) {
            element = $(tb)[ii];

            staffid = $(element).find('.allowance_other_new').attr('data-staff-id');

            check_hour = $(element).find('.check_hour').is(":checked");
            salary_bhxh = intVal($(element).find('.salary_bhxh').val());
            salary_responsibility = intVal($(element).find('.salary_responsibility').val());
            salary_position = intVal($(element).find('.salary_position').val());
            responsibility_salary = intVal($(element).find('.responsibility_salary').val());
            allowance = intVal($(element).find('.allowance').val());
            day_number = intVal($(element).find('.day_number').val());
            total_number_day_holiday = intVal($(element).find('.total_number_day_holiday').val());
            total_number_day_lt = intVal($(element).find('.total_number_day_lt').val());
            total_number_day_ch = intVal($(element).find('.total_number_day_ch').val());
            salary_income = intVal($(element).find('.salary_income').val());
            deduct_bhxh = intVal($(element).find('.deduct_bhxh').val());
            deduct_bhyt = intVal($(element).find('.deduct_bhyt').val());
            deduct_bhtn = intVal($(element).find('.deduct_bhtn').val());
            deduct_union = intVal($(element).find('.deduct_union').val());
            allowance_business_fee = intVal($(element).find('.allowance_business_fee').val());
            allowance_responsibility = intVal($(element).find('.allowance_responsibility').val());
            allowance_other = intVal($(element).find('.allowance_other').val());
            allowance_manu = intVal($(element).find('.allowance_manu').val());
            allowance_western = intVal($(element).find('.allowance_western').val());
            allowance_rice = intVal($(element).find('.allowance_rice').val());
            allowance_rice_tc = intVal($(element).find('.allowance_rice_tc').val());
            bonus_holiday = intVal($(element).find('.bonus_holiday').val());
            count_day_work = intVal($(element).find('.total_date').val());
            sales = intVal($(element).find('.sales').val());
            phone = intVal($(element).find('.phone').val());
            gasonline_cars = intVal($(element).find('.gasonline_cars').val());
            motel = intVal($(element).find('.motel').val());
            concurrently = intVal($(element).find('.concurrently').val());
            business_fee_staff = intVal($(element).find('.business_fee_staff').val());
            seniority = intVal($(element).find('.seniority').val());
            business_fee_difference = intVal($(element).find('.business_fee_difference').html());
            number_reduce = intVal($(element).find('.number_reduce').val());
            grand_total_kt = intVal($(element).find('input.grand_total_kt').val());
            grand_total_kl = intVal($(element).find('input.grand_total_kl').val());

            hour_late = intVal($(element).find('.hour_late').val());

            money_hour_late = ((salary_bhxh + salary_responsibility + salary_position + sales) / count_day_work / hour_day) * hour_late;

            $(element).find('.salary_off').html(tnhFormatMoney(money_hour_late));

            day_number_off = intVal($(element).find('.day_number_off').val());

            day_number_off_new = day_number_off / hour_day;

            salary_compensation = ((business_fee_staff + phone + gasonline_cars + motel) / count_day_work / hour_day) * day_number_off;

            $(element).find('.day_number_off_new').html(day_number_off_new > 0 ? day_number_off_new : '');
            $(element).find('.salary_compensation').html(salary_compensation > 0 ? tnhFormatMoney(salary_compensation) : '');

            complete_permission = intVal($(element).find('.complete_permission').val());

            total_number_day_new = (day_number / hour_day) - (total_number_day_holiday + total_number_day_lt + total_number_day_ch);
            <?php
                if($this->cong_fix == 1) {?>
                    total_number_day_new = ((day_number + hour_late) / hour_day) - (total_number_day_holiday + total_number_day_lt + total_number_day_ch);
                <?php }
            ?>
            total_number_day_new = total_number_day_new < 0 ? 0 : total_number_day_new;
            $(element).find('.total_number_day_new').html(total_number_day_new > 0 ? total_number_day_new : '');

            allowance_rice_money = rice_money * allowance_rice;
            allowance_rice_money_tc = allowance_rice_tc;
            $(element).find('.allowance_rice_money').html(tnhFormatMoney(allowance_rice_money));

            salary_income = day_number * ((salary_bhxh + salary_responsibility + salary_position + sales + gasonline_cars + phone + motel + concurrently + business_fee_staff + seniority) / count_day_work / hour_day);
            <?php if($this->cong_fix == 1) {?>
                    salary_income = (day_number + hour_late) * ((salary_bhxh + salary_responsibility + salary_position + sales + gasonline_cars + phone + motel + concurrently + business_fee_staff + seniority) / count_day_work / hour_day);
            <?php } ?>
            salary_off = 0;
            salary_income = salary_income;

            $(element).find('.salary_income_html').html(salary_income > 0 ? tnhFormatMoney(salary_income) : '');
            $(element).find('.salary_income').val((salary_income));

            total_weekday = intVal($(element).find('.total_weekday').val());
            total_sunday = intVal($(element).find('.total_sunday').val());
            total_holiday = intVal($(element).find('.total_holiday').val());
            total_weekday_night = intVal($(element).find('.total_weekday_night').val());
            total_sunday_night = intVal($(element).find('.total_sunday_night').val());

            sunday_money = ((salary_bhxh + concurrently + sales + seniority) / count_day_work / hour_day) * coefficient_sunday * total_sunday;
            holiday_money = ((salary_bhxh + concurrently + sales + seniority) / count_day_work / hour_day) * coefficient_holiday * total_holiday;
            weekday_money = ((salary_bhxh + concurrently + sales + seniority) / count_day_work / hour_day) * coefficient * total_weekday;
            weekday_night_money = ((salary_bhxh + concurrently + sales + seniority) / count_day_work / hour_day) * coefficient_default_night * total_weekday_night;
            sunday_night_money = ((salary_bhxh + concurrently + sales + seniority) / count_day_work / hour_day) * coefficient_sunday_night * total_sunday_night;

            allowance_business_fee = sunday_money + holiday_money + weekday_money + weekday_night_money + sunday_night_money;
            $(element).find('.allowance_business_fee').val((allowance_business_fee));
            $(element).find('.allowance_business_fee_html').html(tnhFormatMoney(allowance_business_fee));

            list_payment = $(element).find('option:selected', 'select.payrollPayment')
            var total_limit = 0;
            $.each(list_payment, function () {
                total_payment = intVal($(this).attr('data-total'));
                total_limit += total_payment;
            });


            data_json_payment = $(element).find('.data_json_payment').val();
            if (data_json_payment != '') {
                data_json_payment = JSON.parse(data_json_payment);
            }

            liData = '';
            if (data_json_payment.length > 0) {
                $.each(data_json_payment, function (k, v) {
                    total_payment = intVal(v.total_sub);
                    total_limit += total_payment;
                    liData +=
                        `<li style="width:200px"><a>${v.paymentPayRoll.code} (${tnhFormatMoney(v.total_sub)})</a></li>`
                });
            }
            outputStatus = `<div class="dropdown" style="text-align: right;">
                    <button class="dropdown-toggle no_background" style="border: 1px solid #03a9f4;color: #03a9f4;" type="button" data-toggle="dropdown">${tnhFormatMoney(total_limit)}</button>
                    <ul class="dropdown-menu right50">
                        ${liData}
                    </ul></div>
            `;
            $(element).find('.show_payment').html(outputStatus);

            deduct_advance = total_limit;

            total_salary_text_check = grand_total_kt - grand_total_kl + salary_income  + allowance_business_fee + allowance_rice_money + allowance_rice_money_tc + complete_permission + salary_compensation - money_hour_late - deduct_bhxh - deduct_bhyt - deduct_bhtn - deduct_union;
            totalAllowance = 0;
            if (dtAllowance.length > 0) {
                $.each(dtAllowance, function (k, v) {
                    total_salary_text_check += intVal($(element).find(`.allowance_other_${v.id}_${staffid}`).val());
                    totalAllowance += intVal($(element).find(`.allowance_other_${v.id}_${staffid}`).val());
                });
            }

            if (dtReduce.length > 0) {
                $.each(dtReduce, function (k, v) {
                    total_salary_text_check -= intVal($(element).find(`.reduce_other_${v.id}_${staffid}`).val());
                });
            }


            if ((allowance_rice_money + allowance_rice_money_tc) > rice_money_max){
                rice_money_max_new = rice_money_max;
            } else {
                rice_money_max_new = allowance_rice_money + allowance_rice_money_tc;
            }

            tax_exemption  = business_fee_difference + rice_money_max_new;

            $(element).find('.tax_exemption').html(!empty(tax_exemption) ? tnhFormatMoney(tax_exemption) : '');


            total_salary_real_text = 0;
            total_salary_text = 0;
            total_allowance_new = 0;
            total_reduce_new = 0;

            total_salary_text = grand_total_kt - grand_total_kl + salary_income + allowance_business_fee + allowance_rice_money + allowance_rice_money_tc + complete_permission + salary_compensation - money_hour_late - deduct_bhxh - deduct_bhyt - deduct_bhtn - deduct_union - deduct_advance;

            allowance_diff_new = 0;
            if (dtAllowance.length > 0) {
                $.each(dtAllowance, function (k, v) {
                    total_allowance_new += intVal($(element).find(`.allowance_other_${v.id}_${staffid}`).val());
                    if (v.type_check == 3) {
                        allowance_diff_new += intVal($(element).find(`.allowance_other_${v.id}_${staffid}`).val());
                    }
                    total_salary_text += intVal($(element).find(`.allowance_other_${v.id}_${staffid}`).val());
                });
            }

            if (dtReduce.length > 0) {
                $.each(dtReduce, function (k, v) {
                    total_reduce_new += intVal($(element).find(`.reduce_other_${v.id}_${staffid}`).val());
                    total_salary_text -= intVal($(element).find(`.reduce_other_${v.id}_${staffid}`).val());
                });
            }

            total_allowance_new += allowance_rice_money;
            total_allowance_new += allowance_rice_money_tc;
            total_allowance_new += salary_compensation;
            $(element).find('.total_allowance').html(tnhFormatMoney(total_allowance_new));
            footer_total_allowance += total_allowance_new;

            total_reduce_other = total_reduce_new + deduct_advance;

            footer_total_reduce += total_reduce_other;
            $(element).find('.total_reduce_other').html(tnhFormatMoney(total_reduce_other));

            total_reduce_bhxh = deduct_bhxh + deduct_bhyt + deduct_bhtn + deduct_union;
            $(element).find('.total_reduce_bhxh').html(tnhFormatMoney(total_reduce_bhxh));
            footer_total_reduce_bhxh += total_reduce_bhxh;

            $(element).find('.allowance_diff').val(tnhFormatMoney(allowance_diff_new));

            total_new = salary_income + allowance_business_fee + total_allowance_new;
            $(element).find('.total').html(tnhFormatMoney(total_new));

            allowance_diff = intVal($(element).find('.allowance_diff').val());

            salary_income_vat = (grand_total_kt - grand_total_kl + salary_income + business_fee_difference + allowance_diff_new + allowance_rice_money + allowance_rice_money_tc + salary_compensation + complete_permission) - money_hour_late - tax_exemption - (deduct_bhxh + deduct_bhyt + deduct_bhtn) - total_reduce_new - money_vat - (number_reduce * money_reduce) - (((business_fee_staff + phone + gasonline_cars) / count_day_work / hour_day) * day_number);

            income_taxes = (grand_total_kt - grand_total_kl + salary_income + business_fee_difference + allowance_diff_new + allowance_rice_money + allowance_rice_money_tc + salary_compensation + complete_permission) - money_hour_late - tax_exemption - (deduct_bhxh + deduct_bhyt + deduct_bhtn + deduct_union) - total_reduce_new - (((business_fee_staff + phone + gasonline_cars) / count_day_work / hour_day) * day_number);
            $(element).find('.income_taxes').html(tnhFormatMoney(income_taxes))

            if (salary_income_vat < 0){
                salary_income_vat = 0;
            }

            $(element).find('.taxable_income').html(tnhFormatMoney(salary_income_vat))

            total_money_vat_check = salary_income_vat;

            if (total_money_vat_check < 0) {
                total_money_vat_check = 0;
            }
            percent_vat = 0;
            total_reduce_vat = 0;
            if (total_money_vat_check <= 5000000) {
                percent_vat = 5;
                total_reduce_vat = 0;
            } else if (total_money_vat_check > 5000000 && total_money_vat_check <= 10000000) {
                percent_vat = 10;
                total_reduce_vat = 250000;
            } else if (total_money_vat_check > 10000000 && total_money_vat_check <= 18000000) {
                percent_vat = 15;
                total_reduce_vat = 750000;
            } else if (total_money_vat_check > 18000000 && total_money_vat_check <= 32000000) {
                percent_vat = 20;
                total_reduce_vat = 1650000;
            } else if (total_money_vat_check > 32000000 && total_money_vat_check <= 52000000) {
                percent_vat = 25;
                total_reduce_vat = 3250000;
            } else if (total_money_vat_check > 52000000 && total_money_vat_check <= 80000000) {
                percent_vat = 30;
                total_reduce_vat = 5850000;
            } else if (total_money_vat_check > 80000000) {
                percent_vat = 35;
                total_reduce_vat = 9850000;
            }
            total_vat = 0;
            total_vat = (total_money_vat_check * percent_vat / 100) - total_reduce_vat;
            $(element).find('.total_vat').html(tnhFormatMoney(total_vat));

            if (deduct_advance > 0 && (deduct_advance > (total_salary_text_check - total_vat))) {
                count_error++;
                $(element).find('.text-error').html('Số tiền tạm ứng phải nhỏ hơn hoặc bằng tổng thu nhập');
            } else {
                $(element).find('.text-error').html('');
            }

            footer_total_vat += total_vat;

            total_salary_real_text = total_salary_text - total_vat;
            $(element).find('.total_real').html(tnhFormatMoney(total_salary_real_text));


            footer_sales += sales;
            footer_phone += phone;
            footer_gasonline_cars += gasonline_cars;
            footer_motel += motel;
            footer_concurrently += concurrently;
            footer_business_fee_staff += business_fee_staff;
            footer_seniority += seniority;
            footer_salary_responsibility += salary_responsibility;
            footer_salary_position += salary_position;
            footer_day_number_new += total_number_day_new;
            footer_day_number_holiday += total_number_day_holiday;
            footer_day_number_lt += total_number_day_lt;
            footer_day_number_ch += total_number_day_ch;
            footer_allowance_rice_tc += allowance_rice_money_tc;
            footer_responsibility_salary += responsibility_salary;
            footer_salary_all += salary_bhxh + salary_responsibility + salary_position + responsibility_salary;
            footer_total_phu_cap += concurrently + sales + phone + gasonline_cars + motel;

            footer_day_number_off += day_number_off;
            footer_day_number_off_new += day_number_off_new;
            footer_salary_off += money_hour_late;

            footer_hour_late += hour_late;
            footer_money_hour_late += money_hour_late;

            footer_salary_bhxh += salary_bhxh;
            footer_allowance += allowance;
            footer_total_salary_new += (salary_bhxh + allowance);
            footer_day_number += day_number;
            footer_salary_income += salary_income;
            footer_allowance_responsibility += allowance_responsibility;
            footer_allowance_other += allowance_other;
            footer_allowance_manu += allowance_manu;
            footer_allowance_western += allowance_western;
            footer_allowance_business_fee += allowance_business_fee;
            footer_allowance_business_fee_new += business_fee_difference;
            footer_allowance_rice += allowance_rice;
            footer_allowance_rice_money += allowance_rice_money;
            footer_bonus_holiday += bonus_holiday;
            footer_deduct_bhxh += deduct_bhxh;
            footer_deduct_bhyt += deduct_bhyt;
            footer_deduct_bhtn += deduct_bhtn;
            footer_deduct_union += deduct_union;
            footer_deduct_advance += deduct_advance;
            footer_total += total_new;
            footer_total_grand_total_kt += grand_total_kt;
            footer_total_grand_total_kl += grand_total_kl;
            footer_total_real += total_salary_real_text;


        }


        $('.dataTables_scrollFoot').find('.footer_salary_all').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_salary_all) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_salary_bhxh').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_salary_bhxh) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_salary_responsibility').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_salary_responsibility) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_salary_position').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_salary_position) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_sales_salary').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_sales) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_phone_salary').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_phone) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_gasonline_cars_salary').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_gasonline_cars) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_motel_salary').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_motel) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_concurrently_salary').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_concurrently) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_business_fee_staff_salary').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_business_fee_staff) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_phu_cap').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_total_phu_cap) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_seniority').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_seniority) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance').html('<div class="text-right bold">' + tnhFormatMoney(
            footer_allowance) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_salary_new').html(
            '<div class="text-right bold">' + tnhFormatMoney(
                footer_total_salary_new) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_day_number').html('<div class="text-center bold">' +
            tnhFormatNumber(
                footer_day_number) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_day_number_new').html('<div class="text-center bold">' +
            (footer_day_number_new) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_day_number_holiday').html('<div class="text-center bold">' +
            (footer_day_number_holiday) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_day_number_lt').html('<div class="text-center bold">' +
            (footer_day_number_lt) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_day_number_ch').html('<div class="text-center bold">' +
            (footer_day_number_ch) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_day_number_off').html('<div class="text-center bold">' +
            (footer_day_number_off) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_day_number_off_new').html('<div class="text-center bold">' +
            (footer_day_number_off_new) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_hour_late').html('<div class="text-center bold">' +
            (footer_hour_late) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_salary_off').html('<div class="text-right bold">' +
            tnhFormatMoney(footer_salary_off) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_money_hour_late').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_money_hour_late) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_salary_income').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_salary_income) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_responsibility').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_allowance_responsibility) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_other').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_allowance_other) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_manu').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_allowance_manu) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_western').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_allowance_western) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_business_fee').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_allowance_business_fee) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_rice').html('<div class="text-center bold">' +
            tnhFormatNumber(
                footer_allowance_rice) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_rice_tc').html('<div class="text-center bold">' +
            tnhFormatNumber(
                footer_allowance_rice_tc) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_rice_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_allowance_rice_money) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_total_allowance) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_total) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_deduct_bhxh').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_deduct_bhxh) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_deduct_bhyt').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_deduct_bhyt) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_deduct_bhtn').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_deduct_bhtn) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_deduct_union').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_deduct_union) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_deduct_advance').html(
            '<div class="text-right bold">' + tnhFormatMoney(
                footer_deduct_advance) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_reduce').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_total_reduce) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_reduce_bhxh').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_total_reduce_bhxh) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_grand_total_kt').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_total_grand_total_kt) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_grand_total_kl').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_total_grand_total_kl) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_allowance_business_fee_new').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_allowance_business_fee_new) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_vat').html('<div class="text-right bold">' +
            tnhFormatMoney(
                footer_total_vat) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_real').html(
            '<div class="text-right bold">' + tnhFormatMoney(
                footer_total_real) + '</div>');
    }
</script>