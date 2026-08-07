<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
        $('#month_fin,#year_fin,#id_new').on('change', function() {
          $('.table-report_financial').DataTable().ajax.reload();
        });
        var diary_of_collecting_money = $('#diary-of-collecting-money');
        var diary_of_spending_money = $('#diary-of-spending-money');
        var diary_of_revenue_and_expenditure = $('#diary-of-revenue-and-expenditure');
        var aggregate_fund_balance = $('#aggregate-fund-balance');
        var cash_book = $('#cash-book');
        var cash_book_bank = $('#cash-book-bank');
        var cash_flow = $('#cash-flow');
        var account = $('#account');
        var account_bank = $('#account_bank');
        var report_from_choose = $('#report-time');
        var date_range = $('#date-range');
        var report_from = $('input[name="report-from"]');
        var report_to = $('input[name="report-to"]');
        var fnServerParams = {
         "year_fin": '[name="year_fin"]',
         "month_fin": '[name="month_fin"]',
         "id_new": '[name="id_new"]',
         "report_months": '[name="months-report"]',
         "report_from": '[name="report-from"]',
         "report_to": '[name="report-to"]',
         "id_account": '[name="id_account"]',
         "id_account_bank": '[name="id_account_bank"]',


        }
        $('select[name="id_account"],select[name="id_account_bank"]').on('change', function() {
            gen_reports();
        });
        report_from.on('change', function() {
        var val = $(this).val();
        var report_to_val = report_to.val();
        if (val != '') {
          report_to.attr('disabled', false);
          if (report_to_val != '') {
            gen_reports();
          }
        } else {
          report_to.attr('disabled', true);
        }
        });
        $('select[name="months-report"]').on('change', function() {
             var val = $(this).val();
             report_to.attr('disabled', true);
             report_to.val('');
             report_from.val('');
             if (val == 'custom') {
               date_range.addClass('fadeIn').removeClass('hide');
               return;
             } else {
               if (!date_range.hasClass('hide')) {
                 date_range.removeClass('fadeIn').addClass('hide');
               }
             }
             gen_reports();
       });
       report_to.on('change', function() {
         var val = $(this).val();
         if (val != '') {
           gen_reports();
         }
       });
   $('#custom_item_select').on('change', function(e) { 
      var currentQuantityInput = $(e.currentTarget);
      var type = currentQuantityInput.select2('data').type;
      $('#type_items').val(type);
     gen_reports();
   });
     $(function() {
        $('.table-diary-of-collecting-money').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot tr').addClass('alert-header bold warning');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('ch_total_revenue'); ?>");
            $(this).find('tfoot td.total').html(sums.total_amount);
        });
        $('.table-diary-of-spending-money').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot tr').addClass('alert-header bold warning');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('ch_total_expenses'); ?>");
            $(this).find('tfoot td.total').html(sums.total_amount);
        });
        $('.table-diary-of-revenue-and-expenditure').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot tr').addClass('alert-header bold warning');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_dt_table_heading_amount'); ?>");
            $(this).find('tfoot td.thu').html(sums.thu);
            $(this).find('tfoot td.chi').html(sums.chi);
        });
        $('.table-aggregate-fund-balance').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot tr').addClass('alert-header bold warning');
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.debt_start').html(sums.debt_start);
            $(this).find('tfoot td.pay_start').html(sums.pay_start);
            $(this).find('tfoot td.debt').html(sums.debt);
            $(this).find('tfoot td.pay').html(sums.pay);
            $(this).find('tfoot td.debt_end').html(sums.debt_end);
            $(this).find('tfoot td.pay_end').html(sums.pay_end);
        });
        $('.table-cash-book').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot tr').addClass('bold danger');
            $(this).find('tfoot td.total').html(sums.tong);
            row_header();
        });
        $('.table-cash-book-bank').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot tr').addClass('bold danger');
            $(this).find('tfoot td.total').html(sums.tong);
            row_header();
        });
        function row_header() {
            var class_tr = $('.alert-headertext');
            $.each(class_tr, function(index, value) {
                var data = $(value).find('td').first().html();
                $(value).find('td:eq(1), td:eq(2), td:eq(3)').remove();
                $(value).find('td:eq(0)').attr('colspan', 4);
                $(value).find('td:eq(0)').addClass('text-center');
                $(value).find('td:eq(3)').addClass('text-right');
                $(value).css({
                    'font-weight': 'bold'
                });
            })
        }
     });
    function init_report(e, type) {
        $('select[name="months-report"]').selectpicker('val', 'this_month');
        $('head title').html($(e).text());
        $('.title_ch').html($(e).text().toUpperCase());
        account.addClass('hide');
        cash_flow.addClass('hide');
        account_bank.addClass('hide');
        report_from_choose.addClass('hide');
        diary_of_collecting_money.addClass('hide');
        diary_of_spending_money.addClass('hide');
        diary_of_revenue_and_expenditure.addClass('hide');
        aggregate_fund_balance.addClass('hide');
        cash_book.addClass('hide');
        cash_book_bank.addClass('hide');


        var report_wrapper = $('#report');
        if (report_wrapper.hasClass('hide')) {
            report_wrapper.removeClass('hide');
        }
        diary_of_collecting_money.addClass('hide');
        if (type == 'diary-of-collecting-money') {
         report_from_choose.removeClass('hide');
         diary_of_collecting_money.removeClass('hide');
        }
        if (type == 'diary-of-spending-money') {
         report_from_choose.removeClass('hide');
         diary_of_spending_money.removeClass('hide');
        }
        if (type == 'diary-of-revenue-and-expenditure') {
         report_from_choose.removeClass('hide');
         diary_of_revenue_and_expenditure.removeClass('hide');
        }
        if (type == 'aggregate-fund-balance') {
         report_from_choose.removeClass('hide');
         aggregate_fund_balance.removeClass('hide');
        }
        if (type == 'cash-book') {
         report_from_choose.removeClass('hide');
         cash_book.removeClass('hide');
         account.removeClass('hide');
        }
        if (type == 'cash-book-bank') {
         report_from_choose.removeClass('hide');
         cash_book_bank.removeClass('hide');
         account_bank.removeClass('hide');
        }
        if (type == 'cash-flow') {
         cash_flow.removeClass('hide');
        }
        gen_reports();
    }

    function diary_of_collecting_money_v2(){
        if ($.fn.DataTable.isDataTable('.table-diary-of-collecting-money')) {
        $('.table-diary-of-collecting-money').DataTable().destroy();
        }
        initDataTable('.table-diary-of-collecting-money', admin_url + 'reports/diary_of_collecting_money', false, false, fnServerParams, [0, 'asc']);
    }
    function diary_of_spending_money_v2(){
        if ($.fn.DataTable.isDataTable('.table-diary-of-spending-money')) {
        $('.table-diary-of-spending-money').DataTable().destroy();
        }
        initDataTable('.table-diary-of-spending-money', admin_url + 'reports/diary_of_spending_money', false, false, fnServerParams, [0, 'asc']);
    }
    function diary_of_revenue_and_expenditure_v2(){
        if ($.fn.DataTable.isDataTable('.table-diary-of-revenue-and-expenditure')) {
        $('.table-diary-of-revenue-and-expenditure').DataTable().destroy();
        }
        initDataTable('.table-diary-of-revenue-and-expenditure', admin_url + 'reports/diary_of_revenue_and_expenditure', false, false, fnServerParams, [0, 'asc']);
    }
    function aggregate_fund_balance_v2(){
        if ($.fn.DataTable.isDataTable('.table-aggregate-fund-balance')) {
        $('.table-aggregate-fund-balance').DataTable().destroy();
        }
        initDataTable('.table-aggregate-fund-balance', admin_url + 'reports/aggregate_fund_balance', false, false, fnServerParams, [0, 'asc']);
    }
    function cash_book_v2(){
        if ($.fn.DataTable.isDataTable('.table-cash-book')) {
        $('.table-cash-book').DataTable().destroy();
        }
        initDataTable('.table-cash-book', admin_url + 'reports/cash_book', false, false, fnServerParams, [0, 'asc']);
    }
    function cash_book_bank_v2(){
        if ($.fn.DataTable.isDataTable('.table-cash-book-bank')) {
        $('.table-cash-book-bank').DataTable().destroy();
        }
        initDataTable('.table-cash-book-bank', admin_url + 'reports/cash_book_bank', false, false, fnServerParams, [0, 'asc']);
    }
    function cash_flow_v2() {
        if ($.fn.DataTable.isDataTable('.table-report_financial')) {
         $('.table-report_financial').DataTable().destroy();
        }
        initDataTable('.table-report_financial', admin_url + 'reports/report_financial', false, false, fnServerParams, [0, 'ASC']);
    }   
    function gen_reports() {
        if (!$(diary_of_collecting_money).hasClass('hide')) {
        diary_of_collecting_money_v2();
        }else if (!$(diary_of_spending_money).hasClass('hide')) {
        diary_of_spending_money_v2();
        }else if (!$(diary_of_revenue_and_expenditure).hasClass('hide')) {
        diary_of_revenue_and_expenditure_v2();
        }else if (!$(aggregate_fund_balance).hasClass('hide')) {
        aggregate_fund_balance_v2();
        }else if (!$(cash_book).hasClass('hide')) {
        cash_book_v2();
        }else if (!$(cash_book_bank).hasClass('hide')) {
        cash_book_bank_v2();
        }else if (!$(cash_flow).hasClass('hide')) {
        cash_flow_v2();
        }
    }
    function view_pay_slip(id) {
        $('#view_pay_slip_data').html('');
        $.get(admin_url + 'pay_slip/electronic_bill/'+id).done(function(response) {
        $('#view_pay_slip_data').html(response);
        $('#view_pay_slip').modal({show:true,backdrop:'static'});
        init_selectpicker();
        init_datepicker();
        }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
        }); 
    }
    $('body').on('hidden.bs.modal', '#view_pay_slip', function() {
        $('#view_pay_slip_data').html('');
    });
    function view_other_payslips(id) {
        $('#view_other_payslips_data').html('');
        $.get(admin_url + 'other_payslips/view_modal/'+id).done(function(response) {
        $('#view_other_payslips_data').html(response);
        $('#view_other_payslips_view').modal({show:true,backdrop:'static'});
        init_selectpicker();
        init_datepicker();
        }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
        }); 
    }
    $('body').on('hidden.bs.modal', '#view_other_payslips_view', function() {
        $('#view_other_payslips_data').html('');
    });  
    function view_costs_detail(id) {
        $('#view_costs_detail').html('');
        $.get(admin_url + 'reports/view_costs_detail/'+id).done(function(response) {
        $('#view_costs_detail').html(response);
        $('#view_costs_detail_modal').modal({show:true,backdrop:'static'});
        init_selectpicker();
        init_datepicker();
        }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
        }); 
    }
    $('body').on('hidden.bs.modal', '#view_costs_detail_modal', function() {
        $('#view_costs_detail').html('');
    });
</script>
