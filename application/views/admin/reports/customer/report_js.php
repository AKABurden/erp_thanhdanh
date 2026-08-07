<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
var base_url = '<?=base_url()?>';

var fnServerParams = {
    "report_months": '[name="months-report"]',
    "report_from": '[name="report-from"]',
    "report_to": '[name="report-to"]',
    "customer_select": '[name="customer_select"]',
    "staff_select": '[name="staff_select"]',
}
$('#staff_select').select2();
$('#customer_select, #staff_select').on('change', function() {
    gen_reports();
});
ajaxSelectCallBack($('#customer_select'), "<?=admin_url('clients/searchCustomers')?>", 0);
function ajaxSelectCallBack(element, url, id, types = '')
{
    if (id > 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: false,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: url + '/' + id+'/'+types,
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0].children[0]);
                    }
                });
            },
            ajax: {
                url: url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        type:$('#type_items').val(),
                        types: types,
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: false,
            ajax: {
                url: url + '/' + $(element).val(),
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        type:-1,
                        types: types,
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if(data.results != null) {
                        var c = [{code_client:'',id: '', text: 'Tất cả'}].concat(data.results);
                        return { results: c };
                    } else {
                        return { results: [{code_client:'',id: '', text: 'No Match Found'}]};
                    }
                }
            },
            formatResult: repoFormatSelection,
            formatSelection: repoFormatSelection,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; }
        });
    }
}

$('select[name="months-report"]').on('change', function() {
    var val = $(this).val();
    $('#report-to').attr('disabled', true);
    $('#report-to').val('');
    $('#report-from').val('');
    if (val == 'custom') {
        $('#date-range').addClass('fadeIn').removeClass('hide');
        return;
    } else {
        if (!$('#date-range').hasClass('hide')) {
            $('#date-range').removeClass('fadeIn').addClass('hide');
        }
    }
    gen_reports();
});

$('#report-from').on('change', function() {
    var val = $(this).val();
    var report_to_val = $('#report-to').val();
    if (val != '') {
        $('#report-to').attr('disabled', false);
        if (report_to_val != '') {
            gen_reports();
        }
    } else {
        $('#report-to').attr('disabled', true);
    }
});

$('#report-to').on('change', function() {
    var val = $(this).val();
    if (val != '') {
        gen_reports();
    }
});

function init_report(e, type) {
    // $('select[name="months-report"]').selectpicker('val', 'this_month').change();
    $('select[name="months-report"]').selectpicker('val', 'this_month');
    $('.main-report').html('');
    $('.view-report').addClass('hide');
    $('head title').html($(e).text().toUpperCase());
    $('.title_ch').html($(e).text().toUpperCase());

    // date search
    $('#report-time').removeClass('hide');
    $('select[name="months-report"]').selectpicker('val', 'this_month');
    $('#report-from').val('');
    $('#report-to').val('');
    // end
    // customer search
    $('#report-customer').removeClass('hide');
    // end
    //hide staff search
    $('#report-staff').addClass('hide');
    $('#staff_select').select2('val','');
    //end
    
    //show view
    $('#'+type).removeClass('hide');
    //end

    if (type == 'debt-all-result-by-staff') {
        $('#report-customer').addClass('hide');
        $('#report-staff').removeClass('hide');
    }
    gen_reports();
}


function gen_reports() {
    if (!$('#debt-all-result').hasClass('hide')) {
        debt_all_result();
    }
    if (!$('#debt-all-result-by-staff').hasClass('hide')) {
        debt_all_result_by_staff();
    }
    if (!$('#debt-all-result-detail').hasClass('hide')) {
        debt_all_result_detail();
    }
    if (!$('#compare-debt').hasClass('hide')) {
        compare_debt();
    }
    if (!$('#detail-payment').hasClass('hide')) {
        detail_payment();
    }
}

function debt_all_result() {
    if ($.fn.DataTable.isDataTable('.table-debt-all-result')) {
        $('.table-debt-all-result').DataTable().destroy();
    }
    initDataTableReport('.table-debt-all-result', admin_url + 'reports/debt_all_result', false, false, fnServerParams, [0, 'desc']);
}

function debt_all_result_by_staff() {
    if ($.fn.DataTable.isDataTable('.table-debt-all-result-by-staff')) {
        $('.table-debt-all-result-by-staff').DataTable().destroy();
    }
    initDataTable('.table-debt-all-result-by-staff', admin_url + 'reports/debt_all_result_by_staff', false, false, fnServerParams, [0, 'asc']);
}

function debt_all_result_detail() {
    if ($.fn.DataTable.isDataTable('.table-debt-all-result-detail')) {
        $('.table-debt-all-result-detail').DataTable().destroy();
    }
    initDataTableReport('.table-debt-all-result-detail', admin_url + 'reports/debt_all_result_detail', false, false, fnServerParams, [0, 'asc']);
}

function detail_payment(){
    if ($.fn.DataTable.isDataTable('.table-detail-payment')) {
        $('.table-detail-payment').DataTable().destroy();
    }
    if ($("#customer_select").val() == ''){
        // alert_float('danger','Vui lòng chọn khách hàng !');
        // return;
    }
    // initDataTable('.table-detail-payment', admin_url + 'reports/table_detail_payment', false, false, fnServerParams, [1, 'asc']);
     tnhInitDataTable('.table-detail-payment', '<?= site_url('admin/reports/table_detail_payment') ?>', {
        'order': [
            [1, 'asc']
        ],
        'searching': false,
        // 'ordering': false,
        'responsive': true,
        "info" : false,
        "ajax": {
            "url": '<?= site_url('admin/reports/table_detail_payment') ?>',
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnServerParams) {
                    d[key] = $(fnServerParams[key]).val();
                }
                if (table.attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = table.attr('data-last-order-identifier');
                }
            },
            "dataSrc": function(json) {
                return json.aaData;
            }
        },
        "columnDefs": [
        ]
    });
}

function compare_debt() {
    var id_customer = $('#customer_select').val();
    if(id_customer) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id_customer'] = id_customer;
        data['report_months'] = $('#months-report').val();
        data['report_from'] = $('#report-from').val();
        data['report_to'] = $('#report-to').val();
        $.post(admin_url+'reports/compare_debt', data).done(function(response){
            if(typeof response == 'undefined' || response == '') {
                $('.table-compare-debt').find('tbody').html('<tr class="no-select">\
                                                             <td colspan="8">\
                                                               <?=_l('dt_zero_records')?>\
                                                             </td>\
                                                           </tr>');
            }
            else {
                $('.table-compare-debt').find('tbody').html(response);
            }
        });
    }
    else {
        $('.table-compare-debt').find('.total1').html(0);
        $('.table-compare-debt').find('.total2').html(0);
        $('.table-compare-debt').find('.total3').html(0);
    }
}

$('.table-debt-all-result').on('draw.dt', function() {
    var paymentReceivedReportsTable = $(this).DataTable();
    var sums = paymentReceivedReportsTable.ajax.json().sums;
    $(this).find('tfoot').addClass('bold');
    $(this).find('tfoot td.total1').html(sums.total1);
    $(this).find('tfoot td.total2').html(sums.total2);
    $(this).find('tfoot td.total3').html(sums.total3);
    $(this).find('tfoot td.total4').html(sums.total4);
    $(this).find('tfoot td.total5').html(sums.total5);
    $(this).find('tfoot td.total6').html(sums.total6);
});

$('.table-debt-all-result-by-staff').on('draw.dt', function() {
    var paymentReceivedReportsTable = $(this).DataTable();
    var sums = paymentReceivedReportsTable.ajax.json().sums;
    $(this).find('tfoot').addClass('bold');
    $(this).find('tfoot td.total1').html(sums.total1);
    $(this).find('tfoot td.total2').html(sums.total2);
    $(this).find('tfoot td.total3').html(sums.total3);
    $(this).find('tfoot td.total4').html(sums.total4);
    $(this).find('tfoot td.total5').html(sums.total5);
    $(this).find('tfoot td.total6').html(sums.total6);
});

$('.table-debt-all-result-detail').on('draw.dt', function() {
    var paymentReceivedReportsTable = $(this).DataTable();
    var sums = paymentReceivedReportsTable.ajax.json().sums;
    $(this).find('tfoot').addClass('bold');
    $(this).find('tfoot td.total1').html(sums.total1);
    $(this).find('tfoot td.total2').html(sums.total2);
    $(this).find('tfoot td.total3').html(sums.total3);
    $(this).find('tfoot td.total4').html(sums.total4);
});

$('.table-detail-payment').on('draw.dt', function() {
    var paymentReceivedReportsTable = $(this).DataTable();
    var sums = paymentReceivedReportsTable.ajax.json().fotter;
    $(this).find('tfoot').addClass('bold');
    $(this).find('tfoot td.total_quantity').html(sums.total_quantity);
    $(this).find('tfoot td.total_amount_tax').html(sums.total_amount_tax);
    $(this).find('tfoot td.total_amount').html(sums.total_amount);
    row_header(this);
});

function row_header(e) {
    var class_tr = $(e).find('.alert-headertext');
    $.each(class_tr, function(index, value) {
        var data = $(value).find('td').first().html();
        $(value).find('td:eq(3),td:eq(8),td:eq(9),td:eq(4),td:eq(5),td:eq(1),td:eq(2),td:eq(6),td:eq(7),td:eq(10)').remove();
        $(value).find('td:eq(0)').attr('colspan', 11);
    })
}
function viewclient(id = '', client_id = '') {
        $('#viewclient_data').html('');
        $.get(admin_url + 'reports/viewclient_data/' + id + '/' + client_id).done(function(response) {
            $('#viewclient_data').html(response);
            $('#viewclient').modal({
                show: true,
                backdrop: 'static'
            });
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
</script>
