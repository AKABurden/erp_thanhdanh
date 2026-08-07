<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="bold uppercase fsize18 H_title"><?= $title ?></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <label><?= lang('month') ?></label>
                <select name="month" id="month" class="month" data-placeholder="<?= lang('month') ?>" style="width: 100%;" multiple>
                    <?php foreach (getMonth() as $key => $value) : ?>
                        <option <?= date('m') == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label><?= lang('year') ?></label>
                <select name="year" id="year" data-placeholder="<?= lang('year') ?>" style="width: 100%;" multiple>
                    <?php
                    $data = date('Y');
                    for ($i = $data - 5; $i <= $data + 5; $i++) {
                    ?>
                        <option value="<?= $i ?>" <?= ($i == $data) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label><?= lang('tnh_branch') ?></label>
                <?php
                    $branch = $this->site_model->getBranch();
                ?>
                <select name="branch" id="branch" data-placeholder="<?= lang('tnh_branch') ?>" class="" style="width: 100%;" multiple>
                    <option value=""></option>
                    <?php if(!empty($branch)): ?>
                        <?php foreach($branch as $key => $value): ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <hr class="hr-panel-heading">
            <div class="col-md-12 view-sale-performance">
                <h2 class="text-center uppercase text-primary"><?= $title ?></h2>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id=view_pay_slip_data></div>
<div id=view_other_payslips_data></div>
<div id=view_costs_detail></div>
<div id="modal_view"></div>
<div id="view_advance_payment_data"></div>
<div id="view_other_payslips_coupon"></div>
<script>
    function loadSalePerformance() {
        month = $('#month').val();
        precious = $('#precious').val();
        year = $('#year').val();
        branch = $('#branch').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports/loadSalePerformance',
            data: {
                month: month,
                precious: precious,
                year: year,
                branch: branch,
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
            },
            dataType: "html",
            success: function(response) {
                $('.view-sale-performance').html(response);
            }
        });
    }

    $(document).ready(function() {
        $('#year').select2({
            allowClear: true
        });
        $('#month').select2({
            allowClear: true
        });
        $('#precious').select2({
            allowClear: true
        });

        $('#branch').select2({
            allowClear: true
        });

        loadSalePerformance();

        $(document).on('change', '#month, #precious, #year, #branch', function(event) {
            loadSalePerformance();
        });

        $(document).on('click', '#tb-cs_wrapper .btn-dt-reload', function(event) {
            oTableModal.draw();
        });

        $(document).on('click', '#tb-cs-detail_wrapper .btn-dt-reload', function(event) {
            oTableModalDetail.draw();
        });
    });
    function change_expense_report_year(year){
        window.location.href = admin_url+'reports/expenses_vs_income/'+year;
    }
    function view_vouchers_coupon(id) {
        $('#modal_view').html('');
        $.get(admin_url + 'vouchers_coupon/view/' + id).done(function(response) {
            $('#modal_view').html(response);
            $('#view_vouchers_coupon').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    }
    $('body').on('hidden.bs.modal', '#view_vouchers_coupon', function() {
        $('#modal_view').html('');
    });

    function view_pay_slip(id) {
        $('#view_pay_slip_data').html('');
        $.get(admin_url + 'pay_slip/electronic_bill/' + id).done(function(response) {
            $('#view_pay_slip_data').html(response);
            $('#view_pay_slip').modal({
                show: true,
                backdrop: 'static'
            });
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
        $.get(admin_url + 'other_payslips/view_modal/' + id).done(function(response) {
            $('#view_other_payslips_data').html(response);
            $('#view_other_payslips_view').modal({
                show: true,
                backdrop: 'static'
            });
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

    function view_costs_detail_charge(id) {
        $('#view_costs_detail').html('');
        $.get(admin_url + 'reports/view_costs_detail_charge/' + id).done(function(response) {
            $('#view_costs_detail').html(response);
            $('#view_costs_detail_modal').modal({
                show: true,
                backdrop: 'static'
            });
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

    function view_advance_payment(id) {
        $('#view_advance_payment_data').html('');
        $.get(admin_url + 'advance_payment/view_modal/' + id).done(function(response) {
            $('#view_advance_payment_data').html(response);
            $('#view_advance_payment_view').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function edit_other_payslips_coupon_v1(id) {
        $('#view_other_payslips_coupon').html('');
        $.get(admin_url + 'other_payslips_coupon/other_payslips_coupon_v1/' + id).done(function(response) {
            $('#view_other_payslips_coupon').html(response);
            $('#other_payslips_coupon').modal('show');
            init_editor();
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    $('body').on('hidden.bs.modal', '#other_payslips_coupon', function() {
        $('#view_other_payslips_coupon').html('');
    });


    $('body').on('hidden.bs.modal', '#view_advance_payment_view', function() {
        $('#view_advance_payment_data').html('');
    });
</script>