<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    table.dataTable thead>tr>th {
        border-bottom: 1px solid;
    }

    .row-header {
        background: #f2dede;
    }

    .row-footer {
        background: #fff4ce;
    }
</style>
<div id="wrapper" style="min-height: 100%;">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <input id="date_export_ch" class="hide">
                            <div class="col-md-4 border-right">
                                <h4 class="no-margin font-medium">
                                    <i class="fa fa-balance-scale" aria-hidden="true"></i>
                                    <?= $title ?>
                                 </h4>
                                <hr />
                                <?php if (has_permission('debt_all_result', '', 'view')) { ?>
                                    <p>
                                        <a class="font-medium active-debt-all-result" onclick="init_report(this,'debt-all-result'); return false;">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?php echo _l('debt_all_result'); ?>
                                        </a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <?php if (has_permission('debt_all_result_detail', '', 'view')) { ?>
                                    <p>
                                        <a class="font-medium active-debt-all-result-detail" onclick="init_report(this,'debt-all-result-detail'); return false;">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?php echo _l('debt_all_result_detail'); ?>
                                        </a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <?php if (has_permission('debt_all_result_by_staff', '', 'view')) { ?>
                                    <p>
                                        <a class="font-medium active-debt-all-result-by-staff" onclick="init_report(this,'debt-all-result-by-staff'); return false;">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?php echo _l('debt_all_result_by_staff'); ?>
                                        </a>
                                    </p>
                                <?php } ?>
                            </div>
                            <div class="col-md-4 border-right">
                                <h4 class="no-margin font-medium">
                                    <i class="fa fa-balance-scale" aria-hidden="true"></i>
                                </h4>
                                <hr />
                                <?php if (has_permission('debt_all_result', '', 'view')) { ?>
                                    <p>
                                        <a class="font-medium active-sale_listing" onclick="loadReport(this, 'sale_listing')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            Bảng đối chiếu công nợ phải thu
                                        </a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <p>
                                    <a class="font-medium active-detail-payment" onclick="init_report(this, 'detail-payment')">
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                        <?php echo _l('Chi tiết thanh toán'); ?>
                                    </a>
                                </p>
                                <hr class="hr-10" />
                            </div>
                            <!-- <div class="col-md-4 border-right">
                <h4 class="no-margin font-medium">
                  <i class="fa fa-balance-scale" aria-hidden="true"></i>
                  <?= $title ?>
                </h4>
                <hr />
                <?php if (has_permission('compare_debt', '', 'view')) { ?>
                <p>
                  <a class="font-medium" onclick="init_report(this,'compare-debt'); return false;">
                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                    <?php echo _l('compare_debt'); ?>
                  </a>
                </p>
                <?php } ?>
              </div> -->
                            <div class="col-md-4 pull-right">
                                <div class="bg-light-gray border-radius-4">
                                    <div class="p8">
                                        <div class="form-group hide" id="report-time">
                                            <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                                            <select class="selectpicker" name="months-report" id="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                                                <option value="this_month"><?php echo _l('this_month'); ?></option>
                                                <option value="1"><?php echo _l('last_month'); ?></option>
                                                <option value="this_year"><?php echo _l('this_year'); ?></option>
                                                <option value="last_year"><?php echo _l('last_year'); ?></option>
                                                <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                                                <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                                                <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                                                <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="date-range" class="hide mbot15">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light-gray border-radius-4">
                                    <div class="p8">
                                        <div class="form-group hide" id="report-customer">
                                            <label for="" class="control-label"><?php echo _l('clients'); ?></label>
                                            <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="customer_select" id="customer_select" name="customer_select" style="width: 100%">
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light-gray border-radius-4">
                                    <div class="p8">
                                        <div class="form-group hide" id="report-staff">
                                            <label for="" class="control-label"><?php echo _l('staff'); ?></label>
                                            <select style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="staff_select" id="staff_select" name="staff_select" style="width: 100%">
                                                <option value=""></option>
                                                <?php foreach ($staff as $key => $value) { ?>
                                                    <option value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />
                        <h3 style="color: #2b6fa2;font-weight: bold;" class="no-mtop text-center title_ch"><?php echo mb_strtoupper(_l('reports_sales_generated_report'), 'UTF-8'); ?>
                        </h3>
                        <hr class="hr-panel-heading" />
                        <?php $this->load->view('admin/reports/customer/debt_all_result'); ?>
                        <?php $this->load->view('admin/reports/customer/debt_all_result_detail'); ?>
                        <?php $this->load->view('admin/reports/customer/debt_all_result_by_staff'); ?>
                        <?php $this->load->view('admin/reports/customer/compare_debt'); ?>
                        <?php $this->load->view('admin/reports/customer/detail_payment'); ?>
                        <div class="main-report">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="viewclient_data"></div>
<?php init_tail(); ?>
<?php $this->load->view('admin/reports/customer/report_js'); ?>
</body>
<script>
    <?php if(!empty($is_type)) {?>
        $(function () {
            if($(`.active-<?=$is_type?>`).length > 0) {
                $(`.active-<?=$is_type?>`).trigger('click');
            }
        })
    <?php } ?>
</script>

<script>
    function loadReport(el, view) {
        $.ajax({
                url: '<?= base_url('admin/reports/loadSalesReport') ?>',
                type: 'POST',
                dataType: 'html',
                data: {
                    view: view,
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                },
            })
            .done(function(data) {
                $('.main-report').html(data);
                $('.title_ch').html('');
                $('.view-report').addClass('hide');
                $('#report-staff').addClass('hide');
                $('#report-time').addClass('hide');
                $('#report-customer').addClass('hide');
                $('#report-staff').addClass('hide');
            })
            .fail(function() {
                console.log("error");
            });
    }
</script>

</html>