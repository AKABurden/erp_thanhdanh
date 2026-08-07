<?php echo form_open('admin/contracts_sales/convert_contract/'.$id, array('id'=>'form_contracts_sales')); ?>
<div class="modal-dialog modal-lg"  style="width: 50%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_convert_contract'); ?></h4>
		</div>
		<div class="modal-body">
            <?php
                           $colspan = 1;
                           $number_col = '</tr><tr>';
                           $col = 'col-md-4';
                           $type = 'warning';
                     ?>
			<div class="panel-body">
                              <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                 <tbody>
                                    <tr>
                                       <td>
                                          <label for="number" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('code_contract_sales'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <div class="form-group">
                                             <div class="input-group">
                                                <span class="input-group-addon">
                                                   <?php $prefix = get_option('contracts_sales').'-'; ?>
                                                   <?=$prefix?>
                                                   <?=form_hidden('prefix',$prefix)?>
                                                </span>
                                                <?php 
                                                   $number = sprintf('%06d', ch_getMaxID('id', 'tbl_contracts_sales') + 1);
                                                   $value = $number;
                                                ?>
                                                <input type="text" name="code" class="form-control" value="<?= $value ?>" readonly>
                                             </div>
                                          </div>
                                       </td>
                                       <!-- chia ra 2 col khi sưa -->
                                       <?=$number_col?>
                                       <td>
                                          <label for="customer_id" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('clients'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_select('customer_id',$clients,array('id','name'),'', (isset($dataMain) ? ($dataMain->customer_id) : ''),array('disabled'=>true)); ?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="subject" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('title_contract_sales'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_input('subject','', _l('ch_name_contract')); ?>
                                       </td>
                                       <!-- chia ra 2 col khi sưa -->
                                       <?=$number_col?>
                                       <td>
                                          <label for="arr_staff" class="control-label">
                                             <?php echo _l('als_staff'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_select('arr_staff[]',$staff,array('staffid','name'),'','',array('data-actions-box'=>1,'multiple'=>true),array(),'','',false);?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="amount" class="control-label">
                                             <?php echo _l('contract_value'); ?>
                                          </label>
                                       </td>
                                       <td colspan="<?=$colspan?>">
                                          <?php echo render_input('amount','', (isset($dataMain) ? (number_format($dataMain->grand_total)) : ''),'text',array('readonly'=>true,'onkeyup'=>'formatNumBerKeyUp(this)')); ?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="date_start" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('date_start'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_date_input('date_start','',_d(date('Y-m-d'))); ?>
                                       </td>
                                       <?=$number_col?>
                                       <td>
                                          <label for="date_end" class="control-label">
                                             <?php echo _l('date_end'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_date_input('date_end','',''); ?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="description" class="control-label">
                                             <?php echo _l('contract_description'); ?>
                                          </label>
                                       </td>
                                       <td colspan="<?=$colspan?>">
                                          <?php echo render_textarea('description','','') ?>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
		</div>
		<div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        init_selectpicker();
        init_datepicker();
   _validate_form($('#form_contracts_sales'), {
      subject: "required",
      customer_id: "required",
      date_start: "required",
   });
   init_ajax_searchs('customer','#customer_id');
});
function init_ajax_searchs(e, t, a, i) {
   var n = $("body").find(t);
   var h = t;
   if (n.length) {
      var s = {
         ajax: {
             url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
             data: function() {
                 var t = {[csrfData.token_name] : csrfData.hash};
                 return t.type = e, t.rel_id = "", t.q = "{{{q}}}", void 0 !== a && jQuery.extend(t, a), t
             }
         },
         locale: {
             emptyTitle: app.lang.search_ajax_empty,
             statusInitialized: app.lang.search_ajax_initialized,
             statusSearching: app.lang.search_ajax_searching,
             statusNoResults: app.lang.not_results_found,
             searchPlaceholder: app.lang.search_ajax_placeholder,
             currentlySelected: app.lang.currently_selected
         },
         requestDelay: 500,
         cache: !1,
         preprocessData: function(e) {
             var t = [];
             for (var a = e.length, i = 0; i < a; i++) {
                 var n = {
                     value: e[i].id,
                     text: e[i].name
                 }; t.push(n)
             }
             return t;
         },
         preserveSelectedPosition: "after",
         preserveSelected: !0
      };
      n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s);
   }
}



</script>