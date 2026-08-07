<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">

               <div class="panel_s">
                  <div class="additional"></div>
                  <div class="panel-body mbot30">
                      <?php echo form_open($this->uri->uri_string(), array('id' => 'form_contracts_supplier')); ?>
                     <div class="<?=!empty($contracts_supplier) ? 'col-md-4' : ''?>">
                        <div class="panel panel-primary">
                           <div class="panel-heading in-title">
                               <?=!empty($title) ? $title : ''?>
                           </div>
                           <div class="panel-body">
                              <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                 <tbody>
                                    <tr>
                                       <td style="<?=(!empty($contracts_supplier) ? "width: 30%;" : '')?>">
                                          <label for="number" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('Mã Hợp Đồng Mua'); ?>
                                          </label>
                                       </td>
                                       <td style="<?=(!empty($contracts_supplier) ? "width: 70%;" : '')?>">
                                          <div class="form-group">
                                             <div class="input-group">
                                                <span class="input-group-addon">
                                                   <?php $prefix = (!empty($contracts_supplier) ? ($contracts_supplier->prefix . '-') : get_option('contracts_supplier')); ?>
                                                   <?=$prefix?>
                                                   <?=form_hidden('prefix',$prefix)?>
                                                </span>
                                                <?php 
                                                   $number = sprintf('%06d', ch_getMaxID('id', 'tbl_contracts_supplier') + 1);
                                                   $value = (!empty($contracts_supplier) ? ($contracts_supplier->code) : $number);
                                                ?>
                                                <input type="text" name="code" class="form-control" value="<?= $value ?>" readonly>
                                             </div>
                                          </div>
                                       </td>
                                       <?=!empty($contracts_supplier) ? '</tr><tr>' : ''?>
                                       <td>
                                          <label for="supplier_id" class="control-label">
                                             <small class="req text-danger">* </small>
                                             <?php echo _l('Nhà Cung Cấp'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_select('supplier_id',
                                              (!empty($list_suppliers) ? $list_suppliers : []),
                                              ['id','company'],'', (!empty($contracts_supplier) ? ($contracts_supplier->supplier_id) : '')); ?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="subject" class="control-label">
                                             <?php echo _l('Tên Hợp Đồng'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_input('subject','', (!empty($contracts_supplier) ? ($contracts_supplier->subject) : '')); ?>
                                       </td>
                                        <?=!empty($contracts_supplier) ? '</tr><tr>' : ''?>
                                       <td>
                                          <label for="arr_staff" class="control-label">
                                             <?php echo _l('als_staff'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_select('arr_staff[]', (!empty($list_staff) ? $list_staff : []),array('staffid', 'name'),'', (!empty($contracts_supplier) ? (explode(',', $contracts_supplier->arr_staff)) : ''),array('data-actions-box'=>1,'multiple'=>true),array(),'','',false);?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="amount" class="control-label">
                                             <?php echo _l('contract_value'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_input('amount','', (!empty($contracts_supplier) ? (number_format($contracts_supplier->amount)) : ''),'text',['onchange' => 'formatNumBerKeyUp(this)']); ?>
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
                                          <?php echo render_date_input('date_start','', (!empty($contracts_supplier) ? (_d($contracts_supplier->date_start)) : '')); ?>
                                       </td>
                                        <?=!empty($contracts_supplier) ? '</tr><tr>' : ''?>
                                       <td>
                                          <label for="date_end" class="control-label">
                                             <?php echo _l('date_end'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_date_input('date_end','', (!empty($contracts_supplier) ? (_d($contracts_supplier->date_end)) : '')); ?>
                                       </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="date_of_receipt" class="control-label">
                                                <?php echo _l('Ngày tiếp nhận máy'); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php echo render_datetime_input('date_of_receipt','', (!empty($contracts_supplier) ? (_dt($contracts_supplier->date_of_receipt)) : '')); ?>
                                        </td>
                                       <td>
                                          <label for="description" class="control-label">
                                             <?php echo _l('contract_description'); ?>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo render_textarea('description','', (!empty($contracts_supplier) ? ($contracts_supplier->description) : '')); ?>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                      <?php echo form_close(); ?>
                     <!-- mãu hợp đồng -->
                     <?php if(!empty($contracts_supplier)) { ?>
                        <div class="col-md-8">
                           <div class="panel panel-primary">
                              <div class="panel-heading in-title"><?=_l('contract_summary_heading')?></div>
                              <div class="panel-body">
                                 <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                       <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab"><?=_l('contract_content')?></a>
                                    </li>
                                    <li role="presentation">
                                       <a href="#item_file" aria-controls="item_file" role="tab" data-toggle="tab"><?=_l('attachments_file')?></a>
                                    </li>
                                 </ul>

                                  <div class="tab-content">
                                     <!-- tab hợp đồng -->
                                     <div role="tabpanel" class="tab-pane in active" id="item_detail">
                                        <div class="checkbox checkbox-primary">
                                           <input type="checkbox" value="1" id="detail_data" checked>
                                           <label for="detail_data" data-toggle="tooltip" data-original-title=""  title="">Chỉnh sửa</label>

                                           <div class="pull-right">
                                              <a href="<?php echo admin_url('contracts_supplier/pdf/'.$contracts_supplier->id.'?print=true'); ?>" target="_blank" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom"><i class="fa fa-print"></i></a>
                                            </div>
                                        </div>

                                        <div id="_editable" style="border:1px solid #f0f0f0; padding: 10px; margin-top: 15px; display: none;">
                                           <?php
                                              echo render_textarea('editable', '', (!empty($contracts_supplier) ? ($contracts_supplier->content_pdf) : ''), array(), array(), '', 'tinymce');
                                           ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="code_content col-md-12 tc-content" style="border:1px solid #f0f0f0;">
                                            <?=!empty($content_data) ? $content_data : '';?>
                                        </div>
                                     </div>
                                     <!-- end -->
                                     <!-- tab file -->
                                     <div role="tabpanel" class="tab-pane in" id="item_file">
                                         <?php echo form_open(admin_url('contracts_supplier/upload_file/'.$contracts_supplier->id), array(
                                                 'class' => 'dropzone', 'enctype' => 'multipart/form-data', 'id' => 'id-from_upload_file'
                                         ));?>
                                             <div class="dropzone dz-clickable">
                                                 <div class="dz-default dz-message"><span>Thả file vào đây để upload file đính kèm</span></div>
                                            </div>
                                         <?php echo form_close(); ?>
                                        <div class="row">
                                           <div class="panel_s">
                                              <div class="panel-body">
                                                 <?php render_datatable(array(
                                                     _l('name'),
                                                     _l('date_create'),
                                                     _l('expense_delete')
                                                 ),'contracts_supplier_file'); ?>
                                              </div>
                                           </div>
                                        </div>
                                     </div>
                                     <!-- end -->
                                  </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>
                     <!-- end -->
                     <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                        <a class="btn btn-info pull-right submit_contracts_supplier"><?=_l('submit')?></a>
                     </div>
                  </div>
               </div>
         </div>
      </div>
   </div>
</div>

<!-- form upload file -->
<?php if(!empty($contracts_supplier)) { ?>
<!--   <div class="hide">-->
<!--      --><?php //echo form_open(admin_url('contracts_supplier/upload_file/'.$contracts_supplier->id), array('class' => 'form-upload', 'enctype' => 'multipart/form-data'));?>
<!--         <input type="file" class="dropzone-file" name="file">-->
<!--      --><?php //echo form_close(); ?>
<!--   </div>-->
<?php } ?>
<!-- end -->
<?php init_tail(); ?>
<script>
Dropzone.autoDiscover = false;
$(function(){
   _validate_form($('#form_supplier_sales'), {
      subject: "required",
      customer_id: "required",
      date_start: "required",
   });

});

<?php if(!empty($contracts_supplier)) { ?>
   initDataTable('.table-contracts_supplier_file', admin_url+'contracts_supplier/table_contracts_supplier_file/'+<?=$contracts_supplier->id?>, [0], [0], [],[]);
$(function(){
   new Dropzone('#id-from_upload_file', appCreateDropzoneOptions({
       autoProcessQueue: true,
       paramName: "file", // Tên parameter file
       accept: function (file, done) {
           done();
       },
       success: function (file, response) {
           console.log(response)
           response = JSON.parse(response);
           if (response.success) {
               if ($.fn.DataTable.isDataTable($('.table-contracts_supplier_file'))) {
                   $('.table-contracts_supplier_file').DataTable().ajax.reload();
               }
           }
           alert_float(response.alert_type, response.message);
       },
    }))
})


<?php } ?>

<?php if(!empty($contracts_supplier)) { ?>
   $('.submit_contracts_supplier').click(function(e){
      var content = tinymce.get("editable").getContent();
      var dataString= {
          content:content,
          [csrfData['token_name']] : csrfData['hash']
      };

      $.post(admin_url + 'contracts_supplier/edit_pdf/' + <?=$contracts_supplier->id?>, dataString, function(item){
         $('#form_contracts_supplier').submit();
      });
   });
<?php } else { ?>
   $('.submit_contracts_supplier').click(function(e){
      $('#form_contracts_supplier').submit();
   });
<?php } ?>
<?php if(!empty($contracts_supplier)) { ?>
    $('#detail_data').change(function(e){
        if($('#detail_data').prop('checked') == true) {
            $('.code_content').show();
            $('#_editable').hide();
        }
        else {
            $('.code_content').hide();
            $('#_editable').show();
        }
    })
<?php } ?>

function delete_file(id_contracts_supplier,id) {
   var data={[csrfData['token_name']] : csrfData['hash']};
   $.post(admin_url + 'contracts_supplier/delete_file/'+id_contracts_supplier+'/'+id, data, function(item){
      item = JSON.parse(item);
      alert_float(item.alert_type, item.message);
      $('.table-contracts_supplier_file').DataTable().ajax.reload();
   });
}
</script>
</body>
</html>
