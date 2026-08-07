<div class="text-center uppercase">
    <h2><?= lang('tnh_sales_report') ?></h2>
</div>
<hr>
<style>
    #tb-sales-reports tr th:nth-child(1) {
        width: 30px !important;
        min-width: 30px !important;
        max-width: 30px !important;
    }

    #tb-sales-reports tr th:nth-child(2) {
        width: 120px !important;
        min-width: 120px !important;
        max-width: 120px !important;
    }
</style>
<div class="row mbot10">
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('customers', 'customers') ?>
            <input type="text" name="customer_search" onchange="changeTable()" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('year', 'year_search') ?>
            <select name="year_search" id="year_search" class="year_search" style="width: 100%;">
                <option value=""></option>
                <?php
                    for ($i = 0; $i <= 5; $i++) {
                        $year = date('Y', strtotime('-'.$i.' years'));
                        echo '<option '.($year == date('Y') ? 'selected' : '').' value="'.$year.'">'.$year.'</option>';
                    }
                ?>
            </select>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="tb-sales-reports" class="table table-hover table-bordered table-condensed dataTable" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('tnh_numbers') ?></th>
                <th class="text-center"><?= lang('tnh_customer') ?></th>
                <th class="text-center"><?= lang('Tháng 01') ?></th>
                <th class="text-center"><?= lang('Tháng 02') ?></th>
                <th class="text-center"><?= lang('Tháng 03') ?></th>
                <th class="text-center"><?= lang('Tháng 04') ?></th>
                <th class="text-center"><?= lang('Tháng 05') ?></th>
                <th class="text-center"><?= lang('Tháng 06') ?></th>
                <th class="text-center"><?= lang('Tháng 07') ?></th>
                <th class="text-center"><?= lang('Tháng 08') ?></th>
                <th class="text-center"><?= lang('Tháng 09') ?></th>
                <th class="text-center"><?= lang('Tháng 10') ?></th>
                <th class="text-center"><?= lang('Tháng 11') ?></th>
                <th class="text-center"><?= lang('Tháng 12') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <td></td>
                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">

    var oTable = '';
    var params = {
        year_search: '#year_search',
        customers: '#customer_search',
    };

    function changeTable() {
        oTable.draw();
    }

    $(document).ready(function() {
        $('#type_print_search').select2({
            'allowClear': true
        });

        $('#year_search').select2();

        init_datepicker();
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);

        init_datepicker();
        oTable = tnhInitDataTable('#tb-sales-reports', '', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            // 'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getSalesReport') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in params) {
                        d[key] = $(params[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    $('#tb-sales-reports tfoot td:nth-child(3)').html(`<div class="text-right">${tnhFormatMoney(json._total_01)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(4)').html(`<div class="text-right">${tnhFormatMoney(json._total_02)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(5)').html(`<div class="text-right">${tnhFormatMoney(json._total_03)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(6)').html(`<div class="text-right">${tnhFormatMoney(json._total_04)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(7)').html(`<div class="text-right">${tnhFormatMoney(json._total_05)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(8)').html(`<div class="text-right">${tnhFormatMoney(json._total_06)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(9)').html(`<div class="text-right">${tnhFormatMoney(json._total_07)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(10)').html(`<div class="text-right">${tnhFormatMoney(json._total_08)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(11)').html(`<div class="text-right">${tnhFormatMoney(json._total_09)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(12)').html(`<div class="text-right">${tnhFormatMoney(json._total_10)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(13)').html(`<div class="text-right">${tnhFormatMoney(json._total_11)}</div>`);
                    $('#tb-sales-reports tfoot td:nth-child(14)').html(`<div class="text-right">${tnhFormatMoney(json._total_12)}</div>`);
                    return json.aaData;
                }
            },
            "columnDefs": [
            ]
        });
    });
</script>