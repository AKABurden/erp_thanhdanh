<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<!-- <style>
    .table tbody tr td{
        border-bottom: 1px solid #aabbcc;
        border-left: 1px solid #aabbcc;
    }
    .table tbody tr td:last-child{
        border-right: 1px solid #aabbcc;
    }
    .table tbody tr:first-child{
        border-top: 1px solid #aabbcc !important;
    }
    .table tfoot tr td{
        border-bottom: 1px solid #aabbcc;
        border-left: 1px solid #aabbcc;
    }
    .table tfoot tr td:last-child{
        border-right: 1px solid #aabbcc;
    }
</style> -->
<style>
  table.dataTable thead > tr > th {
    border-bottom: 1px solid;
  }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-4 border-right">
                      <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('revenue_and_expenditure'); ?></h4>
                      <hr />
                      <?php if (has_permission('diary_of_collecting_money', '', 'view')){ ?>
                      <p>
                        <a href="" class="font-medium active-diary-of-collecting-money" onclick="init_report(this,'diary-of-collecting-money'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('diary_of_collecting_money'); ?></a>
                      </p>
                      <hr class="hr-10" />
                      <?php } ?>
                      <?php if (has_permission('diary_of_spending_money', '', 'view')){ ?>
                      <p>
                        <a href="" class="font-medium active-diary-of-spending-money" onclick="init_report(this,'diary-of-spending-money'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('diary_of_spending_money'); ?></a>
                      </p>
                      <hr class="hr-10" />
                      <?php } ?>
                      <?php if (has_permission('diary_of_revenue_and_expenditure', '', 'view')){ ?>
                      <p>
                        <a href="" class="font-medium active-diary-of-revenue-and-expenditure" onclick="init_report(this,'diary-of-revenue-and-expenditure'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('diary_of_revenue_and_expenditure'); ?></a>
                      </p>
                      <?php } ?>
                  </div>
                  <div class="col-md-4 border-right">
                      <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('ch_fund_balance'); ?></h4>
                      <hr />
                      <?php if (has_permission('aggregate_fund_balance', '', 'view')){ ?>
                      <p>
                        <a href="" class="font-medium active-aggregate-fund-balance" onclick="init_report(this,'aggregate-fund-balance'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('aggregate_fund_balance'); ?></a>
                      </p>
                      <hr class="hr-10" />
                      <?php } ?>
                      <?php if (has_permission('cash_book', '', 'view')){ ?>
                      <p>
                        <a href="" class="font-medium active-cash-book" onclick="init_report(this,'cash-book'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('cash_book'); ?></a>
                      </p>
                      <hr class="hr-10" />
                      <?php } ?>
                      <?php if (has_permission('cash_book_bank', '', 'view')){ ?>
                      <p>
                        <a href="" class="font-medium active-cash-book-bank" onclick="init_report(this,'cash-book-bank'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('cash_book_bank'); ?></a>
                      </p>
                      <?php } ?>
                  </div>
                <div class="col-md-4">
                  <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('ch_cash_flow_report'); ?></h4>
                      <hr />
                      <?php if (has_permission('cash_flow', '', 'view')){ ?>
                      <p>
                        <a href="" class="font-medium  active-cash-flow" onclick="init_report(this,'cash-flow'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('ch_cash_flow'); ?></a>
                      </p>
                      <?php } ?>
                      <hr class="hr-10" />
                <div class="bg-light-gray border-radius-4">
                  <div class="p8">
                     <div class="form-group hide" id="report-time">
                        <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                        <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
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
                             <input type="text" autocomplete="off" class="form-control datepicker" id="report-from" name="report-from">
                             <div class="input-group-addon">
                                <i class="fa fa-calendar calendar-icon"></i>
                             </div>
                          </div>
                       </div>
                       <div class="col-md-6">
                          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                          <div class="input-group date">
                             <input type="text" autocomplete="off" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                             <div class="input-group-addon">
                                <i class="fa fa-calendar calendar-icon"></i>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
                  <div id="account" class="hide ">
                     <div class="bg-light-gray border-radius-4">
                        <div class="p8">
                      <div class="form-group">
                      <label for="report-to" class="control-label"><?php echo _l('Tài khoản'); ?></label>
                      <select class="selectpicker id_account"  id="id_account" name="id_account"  data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach ($payment_modes as $key => $value) {
                          ?>
                          <option  value="<?=$value['id']?>"><?=$value['name']?></option>
                          <?php 
                        } ?>
                      </select>
                        </div>
                      </div>
                     </div>
                   </div>
                   <div id="account_bank" class="hide ">
                     <div class="bg-light-gray border-radius-4">
                        <div class="p8">
                      <div class="form-group">
                      <label for="report-to" class="control-label"><?php echo _l('tnh_account'); ?></label>
                      <select class="selectpicker id_account_bank"  id="id_account_bank" name="id_account_bank"  data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach ($payment_modes_bank as $key => $value) {
                          ?>
                          <option  value="<?=$value['id']?>"><?=$value['name']?></option>
                          <?php 
                        } ?>
                      </select>
                        </div>
                      </div>
                     </div>
                   </div>
                 </div>
               </div>
               <div id="report" class="hide">
               <hr class="hr-panel-heading" />
               <h3 style="color: #2b6fa2;font-weight: bold;" class="no-mtop text-center title_ch"><?php echo mb_strtoupper(_l('reports_sales_generated_report'), 'UTF-8'); ?></h3>
               <hr class="hr-panel-heading" />
               <?php $this->load->view('admin/reports/fund_balance/diary_of_collecting_money'); ?>
               <?php $this->load->view('admin/reports/fund_balance/diary_of_spending_money'); ?>
               <?php $this->load->view('admin/reports/fund_balance/diary_of_revenue_and_expenditure'); ?>
               <?php $this->load->view('admin/reports/fund_balance/aggregate_fund_balance'); ?>
               <?php $this->load->view('admin/reports/fund_balance/cash_book'); ?>
               <?php $this->load->view('admin/reports/fund_balance/cash_book_bank'); ?>
               <?php $this->load->view('admin/reports/fund_balance/cash_flow'); ?>

               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
   <div id=view_pay_slip_data></div>
   <div id=view_other_payslips_data></div>
   <div id=view_costs_detail></div>
<?php $this->load->view('admin/reports/fund_balance/fund_balance_js'); ?>
<script>
    <?php if(!empty($is_type)) { ?>
    $(function () {
        if($(`.active-<?=$is_type?>`).length > 0) {
            $(`.active-<?=$is_type?>`).trigger('click');
        }
    })
    <?php } ?>
</script>
</body>
</html>
