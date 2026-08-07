<style type="text/css">
  
@import url(https://fonts.googleapis.com/css?family=Open+Sans);
a{text-decoration:none;}
li {list-style-type:none;}

p{font:1em 'Open Sans', sans-serif;}
.tit-nivel3{font:1.5em 'Open Sans', sans-serif;}

ul.tabs {
  overflow: auto;
  height: 300px;
}

ul.tabs li {
  margin: 0;
  cursor: pointer;
  padding: 10px;
  font:1em 'Open Sans', sans-serif;
}

.tab_last {
    background:#900!important;
    margin-top: 50px!important;
    color:#fff!important;
    font:1em 'Open Sans', sans-serif;
}

ul.tabs li:hover {
  background:#bbbbbb2e;
  color:#2885d0;
  border-radius: 5px;
}

ul.tabs li.active {
  background:#bbbbbb2e;
  color:#2885d0;
  border-radius: 5px;
}

.tab_content {
  display: none;
}
.tab_container {
    height: 400px;
  }

.tab_drawer_heading { display: none; }

@media screen and (max-width: 620px) {

  .tab_container {
    width: 100%;
  }

  .tabs {
    display: none;
  }
  .tab_drawer_heading {
    background:#1a1a1a;
    color: #fff;
    border-top: 1px solid #333;
    margin: 0;
    padding: 5px 20px;
    display: block;
    cursor: pointer;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .d_active {
    background-color:#111;
    color: #fff!important;
  }
}
/*hoàng crm bổ xung*/
.panel_box {
  margin: 0;
  box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
}
.center {
  text-align: center;
}
.tab_container i {
  color: red;
  cursor: pointer;
}
.table-scroll {
  max-height: 310px;
  overflow: auto;
}
.wap-right_RFQ {
  height: 400px;
}
.tab-pane{
  display: none;
}
.tab-pane.active{
  display: block;
}
.nav-tabs {
  margin-bottom: 0; 
  background: 0 0; 
  border-radius: 0;
}
.thead-row {
  text-align: center;
  text-transform: uppercase;
  font-weight: 700 !important;
  line-height: 40px;
  background: #3f9ad6;
  color: #fff;
}
.mtop25 {
  margin-top: 25px !important;
}
.padding20 {
  padding: 20px 0 !important;
}
.thead-col {
  text-align: center;
  white-space: unset;
}
.input-col {
  text-align: center;
  border: 0 !important;
  outline: 0 !important;
  border-bottom: 1px solid #9e9e9e !important;
}
.border-bottom {
  border-bottom: 1px solid #9e9e9e !important;
}
.mbottom {
  margin-bottom: 15px;
}
.padding0 {
  padding: 0 !important;
}
.boder-lr {
  border-right: 1px solid #a4a4a4;
  border-left: 1px solid #a4a4a4;
}
.table.table-striped tbody td{
border: 1px solid #f0f0f0;
}
.text-muted {
  color: red !important;
}
/*end*/
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/step_by_step.css')?>">
<div class="modal fade in" id="rdq_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false" style="display: block;">
  <div class="modal-dialog no-modal-header" style="width: 80%">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('ch_rfq_modal'); ?> </span>
            </h4>
          </div>
          <ul class="nav nav-tabs" role="tablist">
             <li role="presentation" class="active">
                <a href="#tab_info" aria-controls="tab_setting" role="tab" data-toggle="tab">
                    <?=_l('ch_purchases_items')?>
                </a>
             </li>
             <li role="presentation">
                <a href="#items" aria-controls="tab_setting" role="tab" data-toggle="tab">
                    <?=_l('Gợi ý giá')?>
                </a>
             </li>
          </ul>
            <?php echo form_open(admin_url('purchases/add_rfq/'.$id), array('id'=>'purchase-form'));?>

          <div role="tabpanel" class="tab-pane active" id="tab_info">
            <div class="modal-body" style="background: #f1f1f1">
              <div class="col-md-8">
                <div class="panel_s panel_box">
                  <div class="panel-body">
                    <div class="tab_container">
                      <input type="text" class="hide" name="id" value="<?=(isset($ask_price) ? $ask_price->id : '')?>">
                       <?php if(!empty($ask_price)){
                          $count = count($suppliers_id);
                          ?>
                        <?php foreach ($suppliers_id as $key => $value) {
                          ?>
                        <div id="tab<?=$value['id']?>" class="tab_content">
                          <a href="#" class="btn btn-success btn-icon" onclick="send_quote_suppliers(<?=$value['id']?>,<?=$ask_price->id?>);return false;" style="float: right;"data-toggle="tooltip" title="" data-placement="top" data-original-title="Gửi cho nhà cung cấp"><i style="color: unset;" class="fa fa-envelope"></i></a>
                          <a href="<?=admin_url('RFQ/excel/'.$ask_price->id.'/'.$value['id'])?>" class="btn btn-info btn-icon" style="float: right;" data-toggle="tooltip" title="" data-placement="top" data-original-title="Xuất excel"><i style="color: unset;"  class="fa fa-file-pdf-o"></i></a>
                          <?php
                          $ktr = get_table_where('tblsupplier_quotes',array('id_ask_price'=>$ask_price->id,'suppliers_id'=>$value['id']),'','row');
                            $disabled = '';
                           ?>
                           <?php if($ask_price->status==2){ if(empty($ktr)){
                            
                            ?>
                          <input type="text" class="hide" id="ktr_<?=$value['id']?>" value="false">
                          <?php if($purchasess->status !=4&&has_permission('supplier_quotes', '', 'create')){?>
                          <a href="<?=admin_url('supplier_quotes/detail_create/'.$ask_price->id.'/'.$value['id'])?>" class="btn btn-info btn-icon" style="float: right;" data-toggle="tooltip" title="" data-placement="top"><?=_l('ch_add_quote')?></a>
                          <?php } }else{
                            if($ktr->status == 2){
                            $disabled = 'disabled';
                            }?>
                          <input type="text" class="hide" id="ktr_<?=$value['id']?>" value="true">
                          <?php if(has_permission('supplier_quotes', '', 'view') || has_permission('supplier_quotes', '', 'view_own')){ ?>
                          <a href="<?=admin_url('supplier_quotes/detail_v2/'.$ktr->id)?>" target='_blank' class="btn btn-info btn-icon" style="float: right;" data-toggle="tooltip" title="" data-placement="top"><?=$ktr->prefix.'-'.$ktr->code?></a> 
                          <?php }?> 
                          <?php } }?>
                            <div class="col-md-8" style="padding-left: 0;">
                              <div class="form-group">
                              <label for="items_ch" class="control-label"><?=_l('items')?></label>
                              <div class="input-group">
                                  <select <?=$disabled?> data-live-search="true" data-id="<?=$value['id']?>" style="width: 200px;"  data-actions-box="1" multiple class="selectpicker no-margin items_ch_<?=$value['id']?>" data-width="100%" id="items_ch" data-none-selected-text="<?php echo _l('add_item'); ?>"  >
                                      <option value=""></option>
                                      <?php foreach ($purchase as $items) { ?>
                                      <option value="<?php echo $items['product_id']; ?>"  data-idd="<?php echo $items['type']; ?>" data-subtext="<?=(!empty($items['check'] == 2)?_l('esit_quote'):'')?>">(<?=format_item_purchases($items['type'])?>)(<?php echo $items['code_item']; ?>) <?php echo $items['name_item']; ?></option>
                                      <?php 
                                      } ?>
                                  </select>
                                  <span class="input-group-btn">
                                    <button class="btn btn-warning load_items" data-id="<?=$value['id']?>" style="height: 36px;" type="button"><?= lang('Load') ?></button>
                                  </span>
                                </div>
                              </div>
                              <?php if($disabled == 'disabled'){ ?>
                                <span style="color: red"><?=_l('supplier_quote_status')?></span>
                              <?php } ?>
                            </div>
                            <script>
                              // $('.items_ch_<?=$value['id']?>').select2();
                            </script>
                            <div class="clearfix"></div>
                            <div class="table-scroll">
                              <table  class="dont-responsive-table table table-striped table-striped_<?=$value['id']?>">
                                  <thead style="background: #3f9ad6;">
                                    <tr>
                                      <th class="center" style="width: 50px;color: #fff;">#</th>
                                      <th class="center" style="width: 135px;color: #fff;"><?=_l('ch_image')?></th>
                                      <th class="center" style="width: 300px;color: #fff;"><?=_l('item_name')?></th>
                                      <th class="center" style="width: 100px;color: #fff;"><?=_l('ch_color')?></th>
                                      <th class="center" style="width: 75px;color: #fff;"><?=_l('invoice_table_quantity_heading')?></th>
                                      <?php if(!empty($ktr)){?>
                                      <th class="center" style="width: 100px;color: #fff;"><?=_l('ch_price')?></th>  
                                      <?php }?>
                                      <th style="width: 30px;color: #fff;"></th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php
                                    $j=0;
                                     foreach ($ask_price->items as $k => $v) {
                                      if($v['suppliers_id'] == $value['id']){
                                        $j++;
                                      ?>
                                      <tr>
                                          <td class="center" style="color: #000;"><input type="text" class="hide" name="items[<?=$value['id']?>][<?=$k?>][product_id]" value="<?=$v['product_id']?>"><?=$j?><input type="text" class="hide" name="items[<?=$value['id']?>][<?=$k?>][type]" value="<?=$v['type']?>"></td>
                                          <td class="text-center">
                                            <div  class="preview_image text-center" style="width: 135px;margin-bottom:0;margin-top:0">
                                                <div class="display-block contract-attachment-wrapper img-<?=$value['id']?>">
                                                    <div>
                                                        <a href="<?=(!empty($v['avatar']) ? (file_exists($v['avatar']) ? base_url($v['avatar']) : (file_exists('uploads/materials/'.$v['avatar']) ? base_url('uploads/materials/'.$v['avatar']) : (file_exists('uploads/products/'.$v['avatar']) ? base_url('uploads/products/'.$v['avatar']) : (file_exists('uploads/tools_supplies/'.$v['avatar']) ? base_url('uploads/tools_supplies/'.$v['avatar']) : base_url('assets/images/preview-not-available.jpg'))))):base_url('assets/images/preview-not-available.jpg'))?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?=(!empty($v['avatar']) ? (file_exists($v['avatar']) ? base_url($v['avatar']) : (file_exists('uploads/materials/'.$v['avatar']) ? base_url('uploads/materials/'.$v['avatar']) : (file_exists('uploads/products/'.$v['avatar']) ? base_url('uploads/products/'.$v['avatar']) : (file_exists('uploads/tools_supplies/'.$v['avatar']) ? base_url('uploads/tools_supplies/'.$v['avatar']) : base_url('assets/images/preview-not-available.jpg'))))):base_url('assets/images/preview-not-available.jpg'))?>">
                                                                <?=format_item_purchases($v['type'])?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                          </td>
                                          <td style="color: #000;">
                                            <?=$v['name_item']?><br>(<?=$v['code_item']?>)
                                          </td>
                                          <td class="center"><?=format_item_color($v['product_id'],$v['type'],1)?></td>
                                          <td class="center" style="color: #000;"><input type="text" class="hide" name="items[<?=$value['id']?>][<?=$k?>][quantity_net]" value="<?=$v['quantity']?>"><?=$v['quantity']?>
                                          </td>
                                          <?php if(!empty($ktr)){?>
                                          <td class="center" style="color: #000;"><?=number_format($v['unit_cost'])?>
                                          </td>
                                          <?php }?>
                                          <td class="center"><?php if(empty($ktr)){?><i onclick="deleteTrItem(this); return false;" class="fa fa-times"></i><?php }?></td>
                                      </tr>
                                    <?php
                                      } 
                                      } ?>
                                  </tbody>
                              </table>
                            </div>
                        </div>
                        <?php 
                        } }else{?>
                          <div class="view_no_ask" style="text-align: center;"><img src="<?=base_url('assets/images/table-no-data.png')?>"></div>
                        <?php }?>
                    </div>
                  </div>
                </div>
              </div> 
              <div class="col-md-4">
                <div class="panel_s panel_box">
                  <div class="panel-body">
                    <div class="wap-right_RFQ">
                      <div style="position: relative;">
                        <?php $disableds =array();if($purchasess->status==4){$disableds =array('disabled'=>true);} if(!empty($ask_price)){   ?>
                        <a style="position: absolute;top: -5px;right: 0;" href="#" class="btn btn-info btn-icon" data-toggle="tooltip" onclick="send_quote_suppliers('-1',<?=$ask_price->id?>);return false;" title="" data-placement="top" data-original-title="Gửi mail tổng"><i style="color: unset;" class="fa fa-envelope"></i></a>
                        <?php }?>
                  <div class="form-group">
                  <label for="supplier_id" class="control-label"><?=_l('supplier')?></label>
                      <select class=" no-margin" data-width="100%" style="width: 100%" id="supplier_id" <?=$disableds?> data-none-selected-text="<?php echo _l('add_item'); ?>"  >
                          <option value=""></option>
                          <?php foreach ($suppliers as $supplier) { ?>
                          <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['company']; ?></option>
                          <?php 
                          } ?>
                      </select>
                  </div>
                      </div>
                      <ul class="tabs">
                        <?php if(!empty($ask_price)){
                          $count = count($suppliers_id);
                          ?>
                        <?php foreach ($suppliers_id as $key => $value) {
                          ?>
                          <?php
                          $ktr = get_table_where('tblsupplier_quotes',array('id_ask_price'=>$ask_price->id,'suppliers_id'=>$value['id']),'','row');
                           ?>
                          <li style="position:relative;" id="tab_<?=$value['id']?>" rel="tab<?=$value['id']?>" class="<?=(($key==($count - 1)) ? 'active' : '')?>"><?=$value['company']?><br><?=!empty($ktr)?'<span class="label" style="background: #e91e63; font-size: 11px;">'._l('esit_quote').'</span>':''?>
                          <?php if(empty($ktr)){?><i id="delete_suppliers_<?=$value['id']?>" onclick="deleteTrItem_tab('<?=$value['id']?>'); return false;" class="fa fa-times" style="top: 0;position: absolute;right: 0;"></i><?php }?></li>
                          <?php 
                        } }?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>      
              <div class="clearfix"></div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="items">
            <div class="modal-body" style="background: #f1f1f1">
              <div class="col-md-12">
                <div class="panel_s panel_box">
                  <div class="panel-body">
                    <div class="tab_container">
                      <?php if(empty($supplier_quotes)){?>
                        <h3 class="text-center">CHƯA CÓ BÁO GIÁ NÀO</h3>
                      <?php }else{?>
                        <h3 class="text-center">GIÁ THẤP NHẤT CỦA CÁC MẶT HÀNG</h3>
                        <table class="dt-tnh table item-purchases table-bordered table-hover" style="width: 100%;">
                          <thead>
                            <tr>
                              <th>Loại</th>
                              <th>Mặt hàng</th>
                              <th>Số lượng</th>
                              <th>Đơn giá</th>
                              <th>Thành tiền</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($supplier_quotes as $key => $value){ ?>
                                <tr data-toggle="tooltip" class="view_child alert-header bold danger" data-type="<?=$value['type']?>" data-id="<?=$value['id_items']?>" style="cursor: pointer;" title="Click vào xem các nhà cung cấp còn lại! Có: <?=count($value['sub'])?> NCC">
                                  <td><?=format_item_purchases($value['type'])?></td>
                                  <td><?=$value['name']?> (<?=$value['code']?>)</td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                </tr>
                                <?php foreach ($value['sub'] as $k => $v){ ?>
                                  <?php if($k == 0){ ?>
                                 <tr>
                                  <td></td>
                                  <td><?=$v['name_supplier']?></td>
                                  <td class="text-center"><?=number_format($v['quantity'])?></td>
                                  <td class="text-right"><?=number_format($v['unit_cost'])?></td>
                                  <td class="text-right"><?=number_format($v['subtotal'])?></td>
                                </tr> 
                                  <?php }else{?>
                                <tr class="view_child_<?=$value['type']?>_<?=$value['id_items']?> hide">
                                  <td></td>
                                  <td><?=$v['name_supplier']?></td>
                                  <td class="text-center"><?=number_format($v['quantity'])?></td>
                                  <td class="text-right"><?=number_format($v['unit_cost'])?></td>
                                  <td class="text-right"><?=number_format($v['subtotal'])?></td>
                                </tr>    
                                  <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </table>
                      <?php }?>

                    </div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>
          <div class="clearfix"></div>
            <div class="modal-footer">
              <?php if(!empty($ask_price)&&has_permission('RFQ', '', 'edit')){?>
                <button  type="submit" class="btn btn-info" ><?=_l('submit')?></button>
              <?php }elseif(empty($ask_price)){?>
                <button  type="submit" class="btn btn-info" ><?=_l('submit')?></button>
              <?php }?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
            </div>
            <?php echo form_close(); ?>
      </div>
  </div>

</div>
<div class="modal fade email-template" id="send_quote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open('admin/RFQ/send_to_email/', array('id' => 'send_mail-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-titlemail" id="myModalLabel">
                   
                </h4>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('cc','CC'); ?>
                            <?php echo render_input('subject', 'ch_subject'); ?>
                            <?php echo render_textarea('content','','',array(),array(),'','tinymce'); ?>
                            <?php echo form_hidden('id'); ?>
                            <?php echo form_hidden('suppliers_mail'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                        <button group="submit" type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"  class="btn btn-info"><?php echo _l('send'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
<script type="text/javascript">
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust();
});
    $(document).ready(function() {
        init_editor();
        
    });
  $(function(){
    $('.item-purchases').dataTable( {
      "bLengthChange" : false,
      "ordering": false,
      "searching": false,
      scrollY:        '43vh',
      "columnDefs": [ {
          "targets"  : [0,1,2,3,4],
          "orderable": false,
        }],
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
    });

  });

    $(function(){
        // validate_invoice_form();
        _validate_form($('#send_mail-form'), {
        email: "required",
        subject: "required",
        content: "required",
    },send_mail);
    });
  function send_mail(form) {
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('#send_quote').modal('hide');
                alert_float('success', response.message);
        $('#send_mail-form').find('button[group="submit"]').button('reset');

            }
        })
        return false;
    }
</script>
<script type="text/javascript">
  $('#supplier_id').select2();
  function send_quote_suppliers(supplier_id,ask_price) {
    $('#send_quote_suppliers').empty();
    $.get(admin_url + 'RFQ/send_quote_suppliers/' + supplier_id + '/' +ask_price).done(function (response) {
      var response = JSON.parse(response);
        $('#subject').val(response.emailtemplates.subject);
        $('#cc').val('');
        $('.modal-titlemail').val(response.title);
        $('#send_mail-form').find('[name="id"]').val(response.id);
        $('#send_mail-form').find('[name="suppliers_mail"]').val(response.suppliers_id);
        init_editor();
        tinymce.get('content').setContent(response.emailtemplates.message);

          
        $('#send_quote').modal('show');
    });
}
$('body').on('hidden.bs.modal', '#send_quote', function() {
     $('#send_mail-form').find('button[group="submit"]').button('reset');
});
$( document ).ready(function() {    
  ready();
});  
$(".tab_content").hide()
$(".tab_content:first").show();
$(".tab_contenttargets:first").show();
var id = <?php echo $id;?>;
function ready() {

  var active = $("ul.tabs li").find('.active');
  $("ul.tabs li").click(function() {
    $(".tab_drawer_heading").removeClass("d_active");
    $(".tab_content").hide();
    var activeTab = $(this).attr("rel"); 
    var length = activeTab.length;
    var ktr = $('#ktr_'+activeTab.slice(3, length)).val();
    // $(".tab_drawer_heading").removeClass("d_active");
      if(ktr === 'true')
      {
        $('#tab_quote_supplier').removeClass('hide');
      }else
      {
        $('#tab_quote_supplier').addClass('hide');
      }
    $("#"+activeTab).fadeIn(1);    
    $("#"+activeTab+'targets').fadeIn(1); 
    $("ul.tabs li").removeClass("active");
    $(this).addClass("active");
    $(".tab_drawer_heading[rel^='"+activeTab+"']").addClass("d_active");

  });
}

  
  var itemList = <?php echo json_encode($suppliers);?>;
    var findItem = (id) => {
        var itemResult;
        $.each(itemList, (index,value) => {
            if(value.id == id) {
                itemResult = value.company;
                return false;
            }
        });
        return itemResult;
    };
  var purchase = <?php echo json_encode($purchase);?>;
    var findpurchase = (id) => {
        var itemResult;
        $.each(purchase, (index,value) => {
            if(value.product_id == id) {
                itemResult = value;
                return false;
            }
        });
        return itemResult;
    };  
    var temtype = <?php echo json_encode($type_items);?>;
    var findItemtypetemtype = (type) => {
        var itemResult;
        $.each(temtype, (index,value) => {
            if(value.type == type) {
                itemResult = '<span class="inline-block label label-warning">'+value.name+'</span>';
                return false;
            }
        });
        return itemResult;
    }; 

  $('#supplier_id').on('change', function(e){
    var supplier_id = $('#supplier_id').val();
    if(supplier_id)
    {

      if($('.wap-right_RFQ').find('#tab_'+supplier_id).length) {
          alert_float('warning', "<?=_l('ch_exsit_suppliers_rfq')?>");
          return;
      }else
      {
      $('.view_no_ask').addClass("hide");
      $("ul.tabs li").removeClass("active");
      $(".tab_content").hide();
      var company = findItem(supplier_id);
      var html = '<li style="position:relative;" id="tab_'+supplier_id+'" rel="tab'+supplier_id+'" class="active">'+company+' <i onclick="deleteTrItem_tab('+supplier_id+'); return false;" class="fa fa-times" style="top: 0;position: absolute;right: 0;"></i></li>';
      var html_tab='<div id="tab'+supplier_id+'" class="tab_content">\
                <div class="col-md-8" style="padding-left: 0;">\
                  <div class="form-group">\
                  <label for="items_ch" class="control-label"><?=_l('items')?></label>\
                  <div class="input-group">\
                      <select data-live-search="true" data-id="'+supplier_id+'" data-actions-box="1" multiple class="selectpicker no-margin items_ch_'+supplier_id+'" style="width:200px" data-width="100%" data-width="100%"  id="items_ch"  data-none-selected-text="<?php echo _l('add_item'); ?>" >\
                          <option value=""></option>\
                          <?php foreach ($purchase as $items) { ?>\
                          <option value="<?php echo $items['product_id']; ?>" data-idd="<?php echo $items['type']; ?>" data-subtext="<?=(!empty($items['check'] == 2)?_l('esit_quote'):'')?>">(<?php echo $items['code_item']; ?>) <?php echo $items['name_item']; ?></option>\
                          <?php 
                          } ?>
                      </select>\
                      <span class="input-group-btn">\
                        <button class="btn btn-warning load_items" data-id="'+supplier_id+'" style="height: 36px;" type="button"><?= lang('Load') ?></button>\
                      </span>\
                  </div>\
                  </div>\
                </div>\
                <div class="clearfix"></div>\
              <div class="table-scroll">\
              <table  class="table table-striped table-striped_'+supplier_id+' dont-responsive-table">\
                  <thead style="background: #3f9ad6;">\
                                    <tr>\
                                      <th class="text-center" style="width: 50px;color: #fff;">#</th>\
                                      <th class="text-center" style="width: 135px;color: #fff;"><?=_l('ch_image')?></th>\
                                      <th class="text-center" style="width: 300px;color: #fff;"><?=_l('item_name')?></th>\
                                      <th class="text-center" style="width: 100px;color: #fff;"><?=_l('ch_color')?></th>\
                                      <th class="text-center" style="width: 75px;color: #fff;"><?=_l('invoice_table_quantity_heading')?></th>\
                                      <th style="width: 30px;color: #fff;"></th>\
                                    </tr>\
                                  </thead>\
                  <tbody>';
      $.post(admin_url + 'purchases/get_items_supplier',{supplier_id:supplier_id,id:id,[csrfData['token_name']] : csrfData['hash']}, function(item){
        var item = JSON.parse(item);
        $.each(item, (index,value) => {
          html_tab+='<tr>\
                      <td><input type="text" class="hide" name="items['+supplier_id+']['+index+'][product_id]" value="'+value.product_id+'"><input type="text" class="hide" name="items['+supplier_id+']['+index+'][type]" value="'+value.type+'">'+(index + 1)+'</td>\
                      <td class="text-center"><img class="mbot5" style="border-radius: 50%;width: 4em;height: 4em;" src="'+value.avatar+'"><br>'+findItemtypetemtype(value.type)+'</td>\
                      <td>\
                        '+value.name_item+'\
                      </td>\
                      <td class="text-center">\
                        '+value.html+'\
                      </td>\
                      <td class="center"><input type="text" class="hide" name="items['+supplier_id+']['+index+'][quantity_net]" value="'+value.quantity_net+'">'+value.quantity_net+'\
                      </td>\
                      <td class="center"><i onclick="deleteTrItem(this); return false;" class="fa fa-times"></i></td>\
                    </tr>';
        });
        html_tab+='</tbody></table>\
          </div>\
          </div>';
        $('.tab_container').append(html_tab);
        $("#tab"+supplier_id).fadeIn();  
        init_selectpicker();
      // $('.items_ch_'+supplier_id).select2();

        });
      $('.tabs').append(html);
      ready();
    }
    }
  });



    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
    };
    var deleteTrItem_tab = (supplier_id) => {
      $('#tab_'+supplier_id).remove();
      $('#tab'+supplier_id).remove();
    };
    $(function(){
    appValidateForm($('#purchase-form'),{},manage_rfq);
  });
    function manage_rfq(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
                alert_float('success',response.message);
                tAPI.draw('page');
            }else
            {
              alert_float('danger',response.message);
            }
            $('#rdq_modal').modal('hide');
        });
        return false;
    } 

    $('body').on('hidden.bs.modal', '#rdq_modal', function() {
        $('#rdq_modal_data').html('');
        tAPI.draw('page');
    });
    // $('.view_child').on('click', function(e){
    //   type = $(this).attr("data-type"); 
    //   id = $(this).attr("data-id"); 
    //   $('.view_child_'+type+'_'+id).removeClass('hide');
    //   $(this).addClass('off_child');
    //   $(this).removeClass('view_child');
    // });
    $(document).on("click",".view_child",function(e) {
      type = $(e.currentTarget).attr("data-type"); 
      id = $(e.currentTarget).attr("data-id"); 
      $('.view_child_'+type+'_'+id).removeClass('hide');
      $(e.currentTarget).addClass('off_child');
      $(e.currentTarget).removeClass('view_child');
    });
    $(document).on("click",".off_child",function(e) {
      type = $(e.currentTarget).attr("data-type"); 
      id = $(e.currentTarget).attr("data-id"); 
      $('.view_child_'+type+'_'+id).addClass('hide');
      $(e.currentTarget).addClass('view_child');
      $(e.currentTarget).removeClass('off_child');
    });
</script>
<script src="<?=base_url('assets/js/step_by_step.js')?>"></script>