<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .tab-pane{
      display: none;
   }
   .tab-pane.active{
      display: block;
   }
   .tc-content {
      min-height: 396px;
      max-height: 396px;
      overflow: auto;
   }
   #_editable {
      min-height: 390px;
      max-height: 390px;
      overflow: auto;
   }
   input.dropzone-file {
      display: none;
   }
   div.dropzone {
      border: 3px dashed #eef2f4;
   }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <?php echo form_open($this->uri->uri_string(), array('id' => 'form_contracts_sales')); ?>
               <div class="panel_s">
                  <div class="additional"></div>
                  <div class="panel-body mbot30">
                     <?php
                        $colspan = 3;
                        $number_col = '';
                        $col = 'col-md-12';
                        $type = 'success';
                        $title = _l('cong_add_new');
                        if(isset($dataMain)) {
                           $colspan = 1;
                           $number_col = '</tr><tr>';
                           $col = 'col-md-4';
                           $type = 'warning';
                           $title = _l('cong_edit');
                        }
                     ?>
                     <div class="ribbon <?=$type?>">
                        <span><?=$title?></span>
                     </div>
                     <div class="<?=$col?>">
                        <div class="panel panel-primary">
                           <div class="panel-heading"><?=$title?></div>
                           <div class="panel-body">
                              <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                 <tbody>
                                    <tr>
                                       <td style="<?=(isset($dataMain) ? "width: 30%;" : '')?>">
                                          <label for="number" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('code_contract_sales'); ?>
                                          </label>
                                       </td>
                                       <td style="<?=(isset($dataMain) ? "width: 70%;" : '')?>">
                                          <div class="form-group">
                                             <div class="input-group">
                                                <span class="input-group-addon">
                                                   <?php $prefix = (isset($dataMain) ? ($dataMain->prefix) : get_option('contracts_sales').'-'); ?>
                                                   <?=$prefix?>
                                                   <?=form_hidden('prefix',$prefix)?>
                                                </span>
                                                <?php 
                                                   $number = sprintf('%06d', ch_getMaxID('id', 'tbl_contracts_sales') + 1);
                                                   $value = (isset($dataMain) ? ($dataMain->code) : $number);
                                                ?>
                                                <input type="text" name="code" class="form-control" value="<?= $value ?>" readonly>
                                             </div>
                                          </div>
                                       </td>
                                       <!-- chia ra 2 col khi sửa -->
                                       <?=$number_col?>
                                       <td>
                                          <label for="customer_id" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('clients'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_select('customer_id',$clients,array('id','name'),'', (isset($dataMain) ? ($dataMain->customer_id) : '')); ?>
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
                                          <?php echo render_input('subject','', (isset($dataMain) ? ($dataMain->subject) : '')); ?>
                                       </td>
                                       <!-- chia ra 2 col khi sửa -->
                                       <?=$number_col?>
                                       <td>
                                          <label for="arr_staff" class="control-label">
                                             <?php echo _l('als_staff'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_select('arr_staff[]',$staff,array('staffid','name'),'', (isset($dataMain) ? (explode(',', $dataMain->arr_staff)) : ''),array('data-actions-box'=>1,'multiple'=>true),array(),'','',false);?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="amount" class="control-label">
                                             <?php echo _l('contract_value'); ?>
                                          </label>
                                       </td>
                                       <td colspan="<?=$colspan?>">
                                          <?php echo render_input('amount','', (isset($dataMain) ? (number_format($dataMain->amount)) : ''),'text',array('onkeyup'=>'formatNumBerKeyUp(this)')); ?>
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
                                          <?php echo render_date_input('date_start','', (isset($dataMain) ? (_d($dataMain->date_start)) : '')); ?>
                                       </td>
                                       <!-- chia ra 2 col khi sửa -->
                                       <?=$number_col?>
                                       <td>
                                          <label for="date_end" class="control-label">
                                             <?php echo _l('date_end'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_date_input('date_end','', (isset($dataMain) ? (_d($dataMain->date_end)) : '')); ?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="description" class="control-label">
                                             <?php echo _l('contract_description'); ?>
                                          </label>
                                       </td>
                                       <td colspan="<?=$colspan?>">
                                          <?php echo render_textarea('description','', (isset($dataMain) ? ($dataMain->description) : '')); ?>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                     <!-- mãu hợp đồng -->
                     <?php if(isset($dataMain)) { ?>
                        <div class="col-md-8">
                           <div class="panel panel-primary">
                              <div class="panel-heading"><?=_l('contract_summary_heading')?></div>
                              <div class="panel-body">
                                 <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                       <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab"><?=_l('contract_content')?></a>
                                    </li>
                                    <li role="presentation">
                                       <a href="#item_file" aria-controls="item_file" role="tab" data-toggle="tab"><?=_l('attachments_file')?></a>
                                    </li>
                                 </ul>
                                 <!-- tab hợp đồng -->
                                 <div role="tabpanel" class="tab-pane active" id="item_detail">
                                    <div class="checkbox checkbox-primary">
                                       <input type="checkbox" value="1" id="detail_data" checked>
                                       <label for="detail_data" data-toggle="tooltip" data-original-title=""  title="">Chỉnh sửa</label>

                                       <div class="pull-right">
                                          <a href="<?php echo admin_url('contracts_sales/pdf/'.$id.'?print=true'); ?>" target="_blank" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom"><i class="fa fa-print"></i></a>
                                          <!-- <a href="<?php echo admin_url('quotes_order/pdf/'.$id); ?>" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                                          <a href="<?php echo admin_url('quotes_order/word/'.$id); ?>" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('Xem Word'); ?>" data-placement="bottom"><i class="fa fa-file-word-o"></i></a> -->
                                       </div>
                                    </div>
                                    
                                    <div id="_editable" style="border:1px solid #f0f0f0; padding: 10px; margin-top: 15px; display: none;">
                                       <?php
                                          echo render_textarea('editable', '', (isset($dataMain) ? ($dataMain->content_pdf) : ''), array(), array(), '', 'tinymce');
                                       ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="code_content col-md-12 tc-content" style="border:1px solid #f0f0f0;"><?=$content_data;?>
                                       <div>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- end -->
                                 <!-- tab file -->
                                 <div role="tabpanel" class="tab-pane" id="item_file">
                                    <div class="dropzone dz-clickable">
                                         <div class="dz-default dz-message"><span>Thả file vào đây để upload file đính kèm</span></div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="panel_s">
                                          <div class="panel-body">
                                             <?php render_datatable(array(
                                                 _l('name'),
                                                 _l('date_create'),
                                                 _l('expense_delete')
                                             ),'contracts_sales_file'); ?>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- end -->
                              </div>
                           </div>
                        </div>
                     <?php } ?>
                     <!-- end -->
                     <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                        <a class="btn btn-info pull-right submit_contracts_sales"><?=_l('submit')?></a>
                     </div>
                  </div>
               </div>
            <?php echo form_close(); ?>
         </div>
      </div>
   </div>
</div>

<!-- form upload file -->
<?php if(isset($dataMain)) { ?>
   <div class="hide">
      <?php echo form_open(admin_url('contracts_sales/upload_file/'.$id), array('class' => 'form-upload', 'enctype' => 'multipart/form-data'));?>
         <input type="file" class="dropzone-file" name="file">
      <?php echo form_close(); ?>
   </div>
<?php } ?>
<!-- end -->
<?php init_tail(); ?>
<script>
Dropzone.autoDiscover = false;
$(function(){
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
<?php if(isset($dataMain)) { ?>
   initDataTable('.table-contracts_sales_file', admin_url+'contracts_sales/table_contracts_sales_file/'+<?=$id?>, [0], [0], [],[]);
   $('.dz-clickable').click(function(e){
      $('.dropzone-file').click();
   });
   $('.dropzone-file').change(function(e){
      $('.form-upload').submit();
   });
<?php } ?>

<?php if(isset($dataMain)) { ?>
   $('.submit_contracts_sales').click(function(e){
      var content = tinymce.get("editable").getContent();
      var dataString={content:content,[csrfData['token_name']] : csrfData['hash']};

      $.post(admin_url + 'contracts_sales/edit_pdf/'+<?=$id?>, dataString, function(item){
         $('#form_contracts_sales').submit();
      });
   });
<?php } else { ?>
   $('.submit_contracts_sales').click(function(e){
      $('#form_contracts_sales').submit();
   });
<?php } ?>
<?php if(isset($dataMain)) { ?>
    $('#detail_data').change(function(e){
        if($('#detail_data').prop('checked')==true) {
            $('.code_content').show();
            $('#_editable').hide();
        }
        else {
            $('.code_content').hide();
            $('#_editable').show();
        }
    })
<?php } ?>

function delete_file(id_contracts_sales,id) {
   var data={[csrfData['token_name']] : csrfData['hash']};
   $.post(admin_url + 'contracts_sales/delete_file/'+id_contracts_sales+'/'+id, data, function(item){
      item = JSON.parse(item);
      alert_float(item.alert_type, item.message);
      $('.table-contracts_sales_file').DataTable().ajax.reload();
   });
}
</script>
</body>
</html>
