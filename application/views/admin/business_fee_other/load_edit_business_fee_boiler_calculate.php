<table id="tb-payroll-salary-overtime" class="table dataTable" style="width: 100%;">
    <thead>
    <?= $tHead ?>
    </thead>
    <tbody>
    <?= $html ?>
    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td class="bold uppercase">Tổng cộng</td>
        <td></td>
        <td class="footer_salary_all"></td>
        <td class="footer_salary"></td>
        <td class="footer_salary_concurrently"></td>
        <td class="footer_salary_sales"></td>
        <td class="footer_salary_seniority"></td>
        <td class="footer_total_sunday"></td>
        <td class="footer_total_sunday_money"></td>
        <td class="footer_total_holiday"></td>
        <td class="footer_total_holiday_money"></td>
        <td class="footer_total_weekday"></td>
        <td class="footer_total_weekday_money"></td>
        <td class="footer_total_weekday_night"></td>
        <td class="footer_total_weekday_night_money"></td>
        <td class="footer_total_sunday_night"></td>
        <td class="footer_total_sunday_night_money"></td>
        <td class="footer_total"></td>
    </tr>
    </tfoot>
</table>
<script>
    var dtHistoryJob;
    gasoline_money = 0;
    construction_money = 0;
    construction_province_money = 0;
    rice_money = 0;
    total_km_new = 0;
    coefficient = "<?= $coefficient ?>";
    coefficient_sunday = "<?= $coefficient_sunday ?>";
    coefficient_holiday = "<?= $coefficient_holiday ?>";
    coefficient_default = "<?= $coefficient_default ?>";
    coefficient_default_night = "<?= $coefficient_default_night ?>";
    coefficient_sunday_night = "<?= $coefficient_sunday_night ?>";
    day_work = "<?= $day_work ?>";
    hour_day = "<?= $hour_day ?>";
    $(document).ready(function () {
        $('.allowance_phone').selectpicker();
        $('.allowance_bike').selectpicker();
        dtHistoryJob = $('#tb-payroll-salary-overtime').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            fixedColumns: {
                leftColumns: 5,
                rightColumns: 0
            },
            scrollY: '400px',
            scrollX: true,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            "drawCallback": function (aoData, settings) {
                $('.allowance_phone').selectpicker();
                $('.allowance_bike').selectpicker();
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {

            },
            "initComplete": function (settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                totalSalaryOvertime();
                mainWrapperHeightFix();
            },
            "footerCallback": function (nRow, aaData, start, end, display) {

            }
        });

    });

    $('.sunday').change(function () {
        totalSalaryOvertime();
    });
    $('.holiday').change(function () {
        totalSalaryOvertime();
    });
    $('.weekday').change(function () {
        totalSalaryOvertime();
    });
    $('.weekday_night').change(function() {
        totalSalaryOvertime();
    });
    $('.sunday_night').change(function() {
        totalSalaryOvertime();
    });
    $('.allowance_phone').change(function () {
        totalSalaryOvertime();
    });
    $('.allowance_bike').change(function () {
        totalSalaryOvertime();
    });

    function totalSalaryOvertime() {
        tb = '#tb-payroll-salary-overtime tbody tr';
        var n = $(tb).length;
        total_salary = 0;
        total_km = 0;
        total_km_money = 0;
        total_sunday = 0;
        total_sunday_money = 0;
        total_holiday = 0;
        total_holiday_money = 0;
        total_weekday = 0;
        total_weekday_money = 0;
        total_weekday_night = 0;
        total_weekday_night_money = 0;
        total_sunday_night = 0;
        total_sunday_night_money = 0;
        total_to_go_night = 0;
        total_to_go_night_money = 0;
        total_allowance = 0;
        total_allowance_money = 0;
        total_allowance_province = 0;
        total_allowance_province_money = 0;
        total_allowance_phone_money = 0;
        total_allowance_bike_money = 0;
        total_allowance_rice = 0;
        total_allowance_rice_money = 0;
        total = 0;
        for (ii = 0; ii < n; ii++) {
            element = $(tb)[ii];


            salary = intVal($(element).find('.salary').val());
            responsibility_salary = intVal($(element).find('.responsibility_salary').val());
            sales = intVal($(element).find('.sales').val());
            gasonline_cars = intVal($(element).find('.gasonline_cars').val());
            phone = intVal($(element).find('.phone').val());
            motel = intVal($(element).find('.motel').val());
            total_km_vs1 = intVal($(element).find('.total_km').val());
            weekday = intVal($(element).find('.weekday').val());
            sunday = intVal($(element).find('.sunday').val());
            holiday = intVal($(element).find('.holiday').val());
            weekday_night = intVal($(element).find('.weekday_night').val());
            sunday_night = intVal($(element).find('.sunday_night').val());
            to_go_noight = intVal($(element).find('.to_go_noight').val());
            construction_allowance = intVal($(element).find('.allowance_survey').val());
            construction_allowance_province = intVal($(element).find('.construction_allowance_province').val());
            rice = intVal($(element).find('.rice').val());

            allowance_phone = intVal($(element).find('select.allowance_phone').find('option:selected').attr('data-total'));
            allowance_bike = intVal($(element).find('select.allowance_bike').find('option:selected').attr('data-total'));


            km_money = (total_km_vs1 / total_km_new) * gasoline_money;

            if (km_money > 0) {
                $(element).find('.km_money').html(`<div class="text-right">${tnhFormatMoney(km_money)}</div>`);
            }

            sunday_money = (salary / day_work / hour_day) * coefficient_sunday * sunday;
            if (sunday_money > 0) {
                $(element).find('.sunday_money').html(`<div class="text-right">${tnhFormatMoney(sunday_money)}</div>`);
            }

            holiday_money = (salary / day_work / hour_day) * coefficient_holiday * holiday;
            if (holiday_money > 0) {
                $(element).find('.holiday_money').html(`<div class="text-right">${tnhFormatMoney(holiday_money)}</div>`);
            }

            weekday_money = (salary / day_work / hour_day) * coefficient * weekday;
            if (weekday_money > 0) {
                $(element).find('.weekday_money').html(`<div class="text-right">${tnhFormatMoney(weekday_money)}</div>`);
            }

            weekday_night_money = (salary/day_work/hour_day) * coefficient_default_night * weekday_night;
            if (weekday_night_money > 0){
                $(element).find('.weekday_night_money').html(`<div class="text-right">${tnhFormatMoney(weekday_night_money)}</div>`);
            }

            sunday_night_money = (salary/day_work/hour_day) * coefficient_sunday_night * sunday_night;
            if (sunday_night_money > 0){
                $(element).find('.sunday_night_money').html(`<div class="text-right">${tnhFormatMoney(sunday_night_money)}</div>`);
            }

            night_money = (salary / day_work / hour_day) * coefficient_default * to_go_noight;
            if (night_money > 0) {
                $(element).find('.night_money').html(`<div class="text-right">${tnhFormatMoney(night_money)}</div>`);
            }

            construction_allowance_money = construction_allowance * construction_money;
            if (construction_allowance_money > 0) {
                $(element).find('.construction_allowance_money').html(`<div class="text-right">${tnhFormatMoney(construction_allowance_money)}</div>`);
            }

            construction_allowance_province_money = construction_allowance_province * construction_province_money;
            if (construction_allowance_province_money > 0) {
                $(element).find('.construction_allowance_province_money').html(`<div class="text-right">${tnhFormatMoney(construction_allowance_province_money)}</div>`);
            }

            rice_money_new = rice * rice_money;
            if (rice_money_new > 0) {
                $(element).find('.rice_money').html(`<div class="text-right">${tnhFormatMoney(rice_money_new)}</div>`);
            }


            total_km += total_km_vs1;
            total_km_money += km_money;
            total_sunday += sunday;
            total_sunday_money += sunday_money;
            total_holiday += holiday;
            total_holiday_money += holiday_money;
            total_weekday += weekday;
            total_weekday_money += weekday_money;
            total_weekday_night += weekday_night;
            total_weekday_night_money += weekday_night_money;
            total_sunday_night += sunday_night;
            total_sunday_night_money += sunday_night_money;
            total_to_go_night += to_go_noight;
            total_to_go_night_money += night_money;
            total_allowance += construction_allowance;
            total_allowance_money += construction_allowance_money;
            total_allowance_province += construction_allowance_province;
            total_allowance_province_money += construction_allowance_province_money;
            total_allowance_phone_money += allowance_phone;
            total_allowance_bike_money += allowance_bike;
            total_allowance_rice += rice;
            total_allowance_rice_money += rice_money_new;

            total_money = sunday_money + holiday_money + weekday_money + weekday_night_money + sunday_night_money;

            total += total_money;

            if (total_money > 0) {
                $(element).find('.total').html(`<div class="text-right">${tnhFormatMoney(total_money)}</div>`);
            }
        }
        $('.dataTables_scrollFoot').find('.footer_total_km').html('<div class="text-center bold">' + tnhFormatNumber(
            total_km) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_km_money').html('<div class="text-right bold">' + tnhFormatMoney(
            total_km_money) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_sunday').html(
            '<div class="text-center bold">' + tnhFormatNumber(
            total_sunday) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_sunday_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_sunday_money) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_holiday').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_holiday) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_holiday_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_holiday_money) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_weekday').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_weekday) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_weekday_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_weekday_money) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_weekday_night').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_weekday_night) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_weekday_night_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_weekday_night_money) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_sunday_night').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_sunday_night) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_sunday_night_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_sunday_night_money) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_to_go_night').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_to_go_night) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_to_go_night_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_to_go_night_money) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_allowance) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_allowance_money) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance_province').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_allowance_province) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance_province_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_allowance_province_money) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance_phone_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_allowance_phone_money) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance_bike_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_allowance_bike_money) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance_rice').html('<div class="text-center bold">' +
            tnhFormatNumber(
                total_allowance_rice) + '</div>');
        $('.dataTables_scrollFoot').find('.footer_total_allowance_rice_money').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total_allowance_rice_money) +
            '</div>');
        $('.dataTables_scrollFoot').find('.footer_total').html('<div class="text-right bold">' +
            tnhFormatMoney(
                total) +
            '</div>');
    }
</script>