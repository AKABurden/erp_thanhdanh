<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   table.dataTable thead>tr>th {
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
                        <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('ch_report_vendor_items'); ?></h4>
                        <hr />
                         <?php if (has_permission('synthetic_purchase', '', 'view')) { ?>
                             <p>
                                 <a href="#" class="font-medium active-general-synthetic-purchase-report" onclick="init_report(this,'general-synthetic-purchase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Tổng hợp phải trả theo PYC'); ?></a>
                             </p>
                             <hr class="hr-10" />
                         <?php } ?>
                         <?php if (has_permission('detail_import_report', '', 'view')) { ?>
                             <p>
                                 <a href="#" class="font-medium active-general-detail-import-report" onclick="init_report(this,'general-detail-import-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Chi tiết phiếu nhập hàng'); ?></a>
                             </p>
                             <hr class="hr-10" />
                         <?php } ?>
                        <?php if (has_permission('purchase_details', '', 'view')) { ?>
                           <p>
                              <a href="#" class="font-medium active-general-purchase-detail-report" onclick="init_report(this,'general-purchase-detail-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('general_purchase_detail_report'); ?></a>
                           </p>
                           <hr class="hr-10" />
                        <?php } ?>
                        <?php if (has_permission('consolidated_purchase_report', '', 'view')) { ?>
                           <p>
                              <a href="#" class="font-medium active-general-purchase-report" onclick="init_report(this,'general-purchase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('ch_consolidated_purchase_report'); ?></a>
                           </p>
                           <hr class="hr-10" />
                        <?php } ?>
                        <?php if (has_permission('purchase_details_book', '', 'view')) { ?>
                           <p>
                              <a href="#" class="font-medium active-detail-purchase-report" onclick="init_report(this,'detail-purchase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('ch_purchase_details_book'); ?></a>
                           </p>
                        <?php } ?>
                        <hr class="hr-10" />
                        <!-- <?php if (has_permission('purchase_details_book', '', 'view')) { ?> -->
                        <p>
                           <a href="#" class="font-medium active-detail-purchase_order-report" onclick="init_report(this,'detail-purchase_order-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('ch_purchase_order_details_book'); ?></a>
                        </p>
                        <!-- <?php } ?> -->
                     </div>
                     <div class="col-md-4 border-right">
                        <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('ch_supplier_debt_statement'); ?></h4>
                        <hr />
                        <?php if (has_permission('summary_of_liabilities', '', 'view')) { ?>
                           <p>
                              <a href="#" class="font-medium active-to_pay_debt-report" onclick="init_report(this,'to_pay_debt-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('ch_summary_of_liabilities'); ?></a>
                           </p>
                           <hr class="hr-10" />
                        <?php } ?>
                        <?php if (has_permission('details_of_liabilities_by_item', '', 'view')) { ?>
                           <p>
                              <a href="#" class="font-medium active-detail_debt-report" onclick="init_report(this,'detail_debt-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('ch_details_of_liabilities_by_item'); ?></a>
                           </p>
                           <hr class="hr-10" />
                        <?php } ?>
                     </div>
                     <div class="col-md-4">
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
                              <div class="form-group hide" id="items">
                                 <input type="text" name="type_items" class="hide" id="type_items">
                                 <label for="" class="control-label"><?php echo _l('Mặt hàng'); ?></label>
                                 <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select" name="custom_item_select" style="width: 100%">
                              </div>
                           </div>
                        </div>
                        <div class="hide" id="supplierts">
                           <div class="bg-light-gray border-radius-4">
                              <div class="p8">
                                 <div class="form-group ">
                                    <?php echo render_select('search_id_suppliers[]', $dataSupplier, array('id', 'company', 'code'), 'ch_name_suppliers', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div id="ch_type_items" class="hide ">
                           <div class="bg-light-gray border-radius-4">
                              <div class="p8">
                                 <div class="form-group">
                                    <label for="report-to" class="control-label"><?php echo _l('Loại giao'); ?></label>
                                    <select class="selectpicker type_import" id="type_import" name="type_import" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                       <option value=""></option>
                                       <option value="1"><?= _l('Giao đủ') ?></option>
                                       <option value="2"><?= _l('Giao 1 phần') ?></option>
                                       <option value="3"><?= _l('Chưa giao') ?></option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <hr class="hr-panel-heading" />
                  <h3 style="color: #2b6fa2;font-weight: bold;" class="no-mtop text-center title_ch"></h3>

                  <hr class="hr-panel-heading" />
                  <div class="report_chart">
                     <?php $this->load->view('admin/reports/purchase/report_chart'); ?>
                  </div>
                  <?php $this->load->view('admin/reports/purchase/general_purchase'); ?>
                  <?php $this->load->view('admin/reports/purchase/detail_purchase'); ?>
                  <?php $this->load->view('admin/reports/purchase/detail_purchase_order'); ?>
                  <?php $this->load->view('admin/reports/purchase/to_pay_debt'); ?>
                  <?php $this->load->view('admin/reports/purchase/detail_debt'); ?>
                  <?php $this->load->view('admin/reports/purchase/general_purchase_detail_report'); ?>
                  <?php $this->load->view('admin/reports/purchase/synthetic_purchase_report'); ?>
                  <?php $this->load->view('admin/reports/purchase/detail_import_report'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/reports/purchase/purchase_js'); ?>

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
<script type="text/javascript">
   $(document).on('change', 'input[name="report-from_ch"]', (e) => {
      var val = $('input[name="report-from_ch"]').val();
      var report_to_val = $('input[name="report-to_ch"]').val();
      if (val != '') {
         if (report_to_val != '') {
            init_dashboard_report();
            init_dashboard_report_cot();
            get_total_limit();
         }
         $('input[name="report-to_ch"]').attr('disabled', false);
      } else {
         $('input[name="report-to_ch"]').attr('disabled', true);
      }
   });
   $('input[name="report-to_ch"]').on('change', function() {
      var val = $('input[name="report-to_ch"]').val();
      if (val != '') {
         init_dashboard_report();
         init_dashboard_report_cot();
         get_total_limit();
      }
   });
   $('select[name="search_id_staff[]"]').on('change', function() {
      var val = $('select[name="search_id_staff[]"]').val();
      init_dashboard_report();
      init_dashboard_report_cot();
      get_total_limit();
   });
   $('[name="customers_ch"]').on('change', function() {
      init_dashboard_report();
      init_dashboard_report_cot();
      get_total_limit();
   });
</script>