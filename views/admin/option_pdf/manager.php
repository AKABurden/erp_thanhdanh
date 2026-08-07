<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
   .tab-pane{
      display: none;
   }
   .tab-pane.active{
      display: block;
   }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
         </div>
      </div>
   </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <ul class="nav nav-tabs" role="tablist">
                     <li role="presentation" class="active">
                        <a href="#tab_purchases" aria-controls="tab_purchases" role="tab" data-toggle="tab">
                            <?php echo _l('ch_purchases'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#tab_supplier_quotes" aria-controls="tab_supplier_quotes" role="tab" data-toggle="tab">
                            <?php echo _l('ch_supplier_quotes'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#tab_purchase_order" aria-controls="tab_purchase_order" role="tab" data-toggle="tab">
                            <?php echo _l('ch_order'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#tab_import" aria-controls="tab_import" role="tab" data-toggle="tab">
                            <?php echo _l('ch_imports'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#tab_return" aria-controls="tab_return" role="tab" data-toggle="tab">
                            <?php echo _l('Trả hàng'); ?>
                        </a>
                     </li>
                  </ul>

                  <!-- tab purchases -->
                  <div role="tabpanel" class="tab-pane active" id="tab_purchases">
                     <div class="panel panel-primary">
                        <div class="panel-heading"><?=_l('field_show')?></div>
                        <div class="panel-body">
                           <?php
                              if(isset($dataMainPurchases->arr_field)) {
                                 $arr = explode(',', $dataMainPurchases->arr_field);
                                 foreach ($arr as $key => $value) {
                                    if($value == 'item_unit_purchases') {
                                       $item_unit_purchases = 'checked';
                                    }
                                    if($value == 'item_quantity_purchases') {
                                       $item_quantity_purchases = 'checked';
                                    }
                                    if($value == 'item_quantity_confirm_purchases') {
                                       $item_quantity_confirm_purchases = 'checked';
                                    }
                                    if($value == 'item_note_purchases') {
                                       $item_note_purchases = 'checked';
                                    }
                                 }
                              }
                           ?>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_unit_purchases) ? $item_unit_purchases : '') ?> data-parent="purchases" data-field="item_unit_purchases">
                                 <label for="field">
                                    <?=_l('item_unit')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_purchases) ? $item_quantity_purchases : '') ?> data-parent="purchases" data-field="item_quantity_purchases">
                                 <label for="field">
                                    <?=_l('item_quantity_all')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_confirm_purchases) ? $item_quantity_confirm_purchases : '') ?> data-parent="purchases" data-field="item_quantity_confirm_purchases">
                                 <label for="field">
                                    <?=_l('item_quantity_confirm')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_note_purchases) ? $item_note_purchases : '') ?> data-parent="purchases" data-field="item_note_purchases">
                                 <label for="field">
                                    <?=_l('note')?>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- tab supplier_quotes -->
                  <div role="tabpanel" class="tab-pane" id="tab_supplier_quotes">
                     <div class="panel panel-primary">
                        <div class="panel-heading"><?=_l('field_show')?></div>
                        <div class="panel-body">
                           <?php
                              if(isset($dataMainSupplier_quotes->arr_field)) {
                                 $arr = explode(',', $dataMainSupplier_quotes->arr_field);
                                 foreach ($arr as $key => $value) {
                                    if($value == 'item_unit_supplier_quotes') {
                                       $item_unit_supplier_quotes = 'checked';
                                    }
                                    if($value == 'item_quantity_supplier_quotes') {
                                       $item_quantity_supplier_quotes = 'checked';
                                    }
                                    if($value == 'item_price_supplier_quotes') {
                                       $item_price_supplier_quotes = 'checked';
                                    }
                                    if($value == 'item_tax_supplier_quotes') {
                                       $item_tax_supplier_quotes = 'checked';
                                    }
                                    if($value == 'item_estimate_total_supplier_quotes') {
                                       $item_estimate_total_supplier_quotes = 'checked';
                                    }
                                    if($value == 'item_note_supplier_quotes') {
                                       $item_note_supplier_quotes = 'checked';
                                    }
                                 }
                              }
                           ?>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_unit_supplier_quotes) ? $item_unit_supplier_quotes : '') ?> data-parent="supplier_quotes" data-field="item_unit_supplier_quotes">
                                 <label for="field">
                                    <?=_l('item_unit')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_supplier_quotes) ? $item_quantity_supplier_quotes : '') ?> data-parent="supplier_quotes" data-field="item_quantity_supplier_quotes">
                                 <label for="field">
                                    <?=_l('item_quantity')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_price_supplier_quotes) ? $item_price_supplier_quotes : '') ?> data-parent="supplier_quotes" data-field="item_price_supplier_quotes">
                                 <label for="field">
                                    <?=_l('ch_price')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_tax_supplier_quotes) ? $item_tax_supplier_quotes : '') ?> data-parent="supplier_quotes" data-field="item_tax_supplier_quotes">
                                 <label for="field">
                                    <?=_l('tax')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_estimate_total_supplier_quotes) ? $item_estimate_total_supplier_quotes : '') ?> data-parent="supplier_quotes" data-field="item_estimate_total_supplier_quotes">
                                 <label for="field">
                                    <?=_l('estimate_total')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_note_supplier_quotes) ? $item_note_supplier_quotes : '') ?> data-parent="supplier_quotes" data-field="item_note_supplier_quotes">
                                 <label for="field">
                                    <?=_l('note')?>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- tab purchase_order -->
                  <div role="tabpanel" class="tab-pane" id="tab_purchase_order">
                     <div class="panel panel-primary">
                        <div class="panel-heading"><?=_l('field_show')?></div>
                        <div class="panel-body">
                           <?php
                              if(isset($dataMainPurchase_order->arr_field)) {
                                 $arr = explode(',', $dataMainPurchase_order->arr_field);
                                 foreach ($arr as $key => $value) {
                                    if($value == 'item_quantity_purchase_order') {
                                       $item_quantity_purchase_order = 'checked';
                                    }
                                    if($value == 'item_quantity_suppliers_purchase_order') {
                                       $item_quantity_suppliers_purchase_order = 'checked';
                                    }
                                    if($value == 'item_unit_purchase_order') {
                                       $item_unit_purchase_order = 'checked';
                                    }
                                    if($value == 'item_price_expected_purchase_order') {
                                       $item_price_expected_purchase_order = 'checked';
                                    }
                                    if($value == 'item_price_suppliers_purchase_order') {
                                       $item_price_suppliers_purchase_order = 'checked';
                                    }
                                    if($value == 'item_amount_expected_purchase_order') {
                                       $item_amount_expected_purchase_order = 'checked';
                                    }
                                    if($value == 'item_promotion_suppliers_purchase_order') {
                                       $item_promotion_suppliers_purchase_order = 'checked';
                                    }
                                    if($value == 'item_tax_purchase_order') {
                                       $item_tax_purchase_order = 'checked';
                                    }
                                    if($value == 'item_amount_suppliers_purchase_order') {
                                       $item_amount_suppliers_purchase_order = 'checked';
                                    }
                                    if($value == 'item_note_purchase_order') {
                                       $item_note_purchase_order = 'checked';
                                    }
                                 }
                              }
                           ?>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_purchase_order) ? $item_quantity_purchase_order : '') ?> data-parent="purchase_order" data-field="item_quantity_purchase_order">
                                 <label for="field">
                                    <?=_l('item_quantity')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_suppliers_purchase_order) ? $item_quantity_suppliers_purchase_order : '') ?> data-parent="purchase_order" data-field="item_quantity_suppliers_purchase_order">
                                 <label for="field">
                                    <?=_l('quantity_suppliers')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_unit_purchase_order) ? $item_unit_purchase_order : '') ?> data-parent="purchase_order" data-field="item_unit_purchase_order">
                                 <label for="field">
                                    <?=_l('item_unit')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_price_expected_purchase_order) ? $item_price_expected_purchase_order : '') ?> data-parent="purchase_order" data-field="item_price_expected_purchase_order">
                                 <label for="field">
                                    <?=_l('price_expected')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_price_suppliers_purchase_order) ? $item_price_suppliers_purchase_order : '') ?> data-parent="purchase_order" data-field="item_price_suppliers_purchase_order">
                                 <label for="field">
                                    <?=_l('price_suppliers')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_amount_expected_purchase_order) ? $item_amount_expected_purchase_order : '') ?> data-parent="purchase_order" data-field="item_amount_expected_purchase_order">
                                 <label for="field">
                                    <?=_l('amount_expected_vnd')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_promotion_suppliers_purchase_order) ? $item_promotion_suppliers_purchase_order : '') ?> data-parent="purchase_order" data-field="item_promotion_suppliers_purchase_order">
                                 <label for="field">
                                    <?=_l('promotion_suppliers')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_tax_purchase_order) ? $item_tax_purchase_order : '') ?> data-parent="purchase_order" data-field="item_tax_purchase_order">
                                 <label for="field">
                                    <?=_l('tax')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_amount_suppliers_purchase_order) ? $item_amount_suppliers_purchase_order : '') ?> data-parent="purchase_order" data-field="item_amount_suppliers_purchase_order">
                                 <label for="field">
                                    <?=_l('amount_suppliers_vnd')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_note_purchase_order) ? $item_note_purchase_order : '') ?> data-parent="purchase_order" data-field="item_note_purchase_order">
                                 <label for="field">
                                    <?=_l('note')?>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- tab import -->
                  <div role="tabpanel" class="tab-pane" id="tab_import">
                     <div class="panel panel-primary">
                        <div class="panel-heading"><?=_l('field_show')?></div>
                        <div class="panel-body">
                           <?php
                              if(isset($dataMainImport->arr_field)) {
                                 $arr = explode(',', $dataMainImport->arr_field);
                                 foreach ($arr as $key => $value) {
                                    if($value == 'item_warehouse_localtion_import') {
                                       $item_warehouse_localtion_import = 'checked';
                                    }
                                    if($value == 'item_unit_import') {
                                       $item_unit_import = 'checked';
                                    }
                                    if($value == 'item_quantity_import') {
                                       $item_quantity_import = 'checked';
                                    }
                                    if($value == 'item_quantity_confirm_import') {
                                       $item_quantity_confirm_import = 'checked';
                                    }
                                    if($value == 'item_price_import') {
                                       $item_price_import = 'checked';
                                    }
                                    if($value == 'item_promotion_suppliers_import') {
                                       $item_promotion_suppliers_import = 'checked';
                                    }
                                    if($value == 'item_tax_import') {
                                       $item_tax_import = 'checked';
                                    }
                                    if($value == 'item_invoice_total_import') {
                                       $item_invoice_total_import = 'checked';
                                    }
                                    if($value == 'item_note_import') {
                                       $item_note_import = 'checked';
                                    }
                                 }
                              }
                           ?>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_warehouse_localtion_import) ? $item_warehouse_localtion_import : '') ?> data-parent="import" data-field="item_warehouse_localtion_import">
                                 <label for="field">
                                    <?=_l('warehouse_localtion')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_unit_import) ? $item_unit_import : '') ?> data-parent="import" data-field="item_unit_import">
                                 <label for="field">
                                    <?=_l('item_unit')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_import) ? $item_quantity_import : '') ?> data-parent="import" data-field="item_quantity_import">
                                 <label for="field">
                                    <?=_l('item_quantity')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_confirm_import) ? $item_quantity_confirm_import : '') ?> data-parent="import" data-field="item_quantity_confirm_import">
                                 <label for="field">
                                    <?=_l('item_quantity_confirm')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_price_import) ? $item_price_import : '') ?> data-parent="import" data-field="item_price_import">
                                 <label for="field">
                                    <?=_l('tnh_price_import')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_promotion_suppliers_import) ? $item_promotion_suppliers_import : '') ?> data-parent="import" data-field="item_promotion_suppliers_import">
                                 <label for="field">
                                    <?=_l('promotion_suppliers')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_tax_import) ? $item_tax_import : '') ?> data-parent="import" data-field="item_tax_import">
                                 <label for="field">
                                    <?=_l('tax')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_invoice_total_import) ? $item_invoice_total_import : '') ?> data-parent="import" data-field="item_invoice_total_import">
                                 <label for="field">
                                    <?=_l('invoice_total')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_note_import) ? $item_note_import : '') ?> data-parent="import" data-field="item_note_import">
                                 <label for="field">
                                    <?=_l('note')?>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- tab return -->
                  <div role="tabpanel" class="tab-pane" id="tab_return">
                     <div class="panel panel-primary">
                        <div class="panel-heading"><?=_l('field_show')?></div>
                        <div class="panel-body">
                           <?php
                              if(isset($dataMainRetrun->arr_field)) {
                                 $arr = explode(',', $dataMainRetrun->arr_field);
                                 foreach ($arr as $key => $value) {
                                    if($value == 'item_warehouse_localtion_Retrun') {
                                       $item_warehouse_localtion_Retrun = 'checked';
                                    }
                                    if($value == 'item_unit_Retrun') {
                                       $item_unit_Retrun = 'checked';
                                    }
                                    if($value == 'item_quantity_confirm_Retrun') {
                                       $item_quantity_confirm_Retrun = 'checked';
                                    }
                                    if($value == 'item_price_Retrun') {
                                       $item_price_Retrun = 'checked';
                                    }
                                    if($value == 'item_tax_Retrun') {
                                       $item_tax_Retrun = 'checked';
                                    }
                                    if($value == 'item_invoice_total_Retrun') {
                                       $item_invoice_total_Retrun = 'checked';
                                    }
                                    if($value == 'item_note_Retrun') {
                                       $item_note_Retrun = 'checked';
                                    }
                                 }
                              }
                           ?>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_warehouse_localtion_Retrun) ? $item_warehouse_localtion_Retrun : '') ?> data-parent="return_suppliers" data-field="item_warehouse_localtion_Retrun">
                                 <label for="field">
                                    <?=_l('warehouse_localtion')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_unit_Retrun) ? $item_unit_Retrun : '') ?> data-parent="return_suppliers" data-field="item_unit_Retrun">
                                 <label for="field">
                                    <?=_l('item_unit')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_quantity_confirm_Retrun) ? $item_quantity_confirm_Retrun : '') ?> data-parent="return_suppliers" data-field="item_quantity_confirm_Retrun">
                                 <label for="field">
                                    <?=_l('Số lượng trả')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_price_Retrun) ? $item_price_Retrun : '') ?> data-parent="return_suppliers" data-field="item_price_Retrun">
                                 <label for="field">
                                    <?=_l('Đơn giá')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_tax_Retrun) ? $item_tax_Retrun : '') ?> data-parent="return_suppliers" data-field="item_tax_Retrun">
                                 <label for="field">
                                    <?=_l('tax')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_invoice_total_Retrun) ? $item_invoice_total_Retrun : '') ?> data-parent="return_suppliers" data-field="item_invoice_total_Retrun">
                                 <label for="field">
                                    <?=_l('invoice_total')?>
                                 </label>
                              </div>
                           </div>
                           <div class="wap-pdf">
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" class="field" <?php echo(isset($item_note_Retrun) ? $item_note_Retrun : '') ?> data-parent="return_suppliers" data-field="item_note_Retrun">
                                 <label for="field">
                                    <?=_l('note')?>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
   $('.field').change(function(e) {
      var target = $(e.currentTarget);
      var parent = target.attr('data-parent');
      var field = target.attr('data-field');
      if(target.prop('checked')) {
         jQuery.ajax({
            type: "post",
            url:admin_url+"option_pdf/add_field",
            data: {[csrfData['token_name']] : csrfData['hash'], parent:parent,field:field},
            cache: false,
            success: function (data) {
            }
        });
      } else {
         jQuery.ajax({
            type: "post",
            url:admin_url+"option_pdf/remove_field",
            data: {[csrfData['token_name']] : csrfData['hash'], parent:parent,field:field},
            cache: false,
            success: function (data) {
            }
        });
      }
   });
</script>