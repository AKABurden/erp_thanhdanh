<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
 <div class="content">
   <div class="row">

  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">
        <?php if (isset($item)) { ?>
        <?php echo form_hidden( 'isedit'); ?>
        <?php echo form_hidden( 'itemid', $item->id); ?>
      <div class="clearfix"></div>
        <?php } ?>
    <h4 class="bold no-margin"><?php  echo (isset($item) ? _l('invoice_item_edit_heading') : _l('invoice_item_add_heading')); ?></h4>
  <hr class="no-mbot no-border" />
  <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l( 'item_detail'); ?>
                </a>
            </li>
            <?php
                $items_custom_fields = false;
                if(total_rows(db_prefix().'customfields',array('fieldto'=>'items','active'=>1)) > 0 ){
                     $items_custom_fields = true;
                 ?>
             <li role="presentation" class="<?php if($this->input->get('tab') == 'custom_fields'){echo 'active';}; ?>">
                <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
                <?php echo hooks()->apply_filters('customer_profile_tab_custom_fields_text', _l( 'custom_fields')); ?>
                </a>
             </li>
             <?php } ?>
            <?php if(isset($item)) { ?>
              <li role="presentation">
                  <a href="#item_price_history" aria-controls="item_price_history" role="tab" data-toggle="tab">
                      <?php echo _l( 'item_price_history'); ?>
                  </a>
              </li>
            <?php } ?>
            <?php 
              if(isset($item) && $item->type==2){?>
             <li role="presentation" class="li_combo">
                  <a href="#combo_items" aria-controls="combo_items" role="tab" data-toggle="tab">
                      <?php echo _l( 'combo_items'); ?>
                  </a>
              </li>
            <?php }?>
            
        </ul>
            <?php echo form_open_multipart($this->uri->uri_string(), array('class'=>'items-form','autocomplete'=>'off')); ?>

           <div class="tab-content">

             <?php hooks()->do_action('after_custom_items_tab_content',isset($item) ? $item->id : false); ?>
             <?php if($items_custom_fields) { ?>
             <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'custom_fields'){echo ' active';}; ?>" id="custom_fields">
                <?php $rel_id=( isset($item) ? $item->id : false); ?>
                <?php echo render_custom_fields('items',$rel_id); ?>
             </div>
             <?php } ?>
            <div role="tabpanel" class="tab-pane active" id="item_detail">
                <div class="row">    
                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

                    <?php 
                        $array_type=array(array('id'=>1,'name'=>'Tiêu chuẩn'),array('id'=>2,'name'=>'COMBO'));
                        $value=(isset($item) ? $item->type : "");
                        // var_dump($item->type);
                        echo render_select('type',$array_type,array('id','name'),'ch_package',$value);
                    ?>

                    <?php
                      // config
                      $attrs_not_select = array('data-none-selected-text'=>_l('system_default_string'));
                    ?>
                    <div class="form-group text-center">
                        <label for="avatar" class="profile-image" style="text-align: left;"><?php echo _l('item_avatar'); ?></label>
                        <input type="file" onchange="readURL(this, '#avatar_view');"  name="item_avatar" class="form-control" id="avatar"> <br />
                        <div class="preview_image" id="avatar_view"  style="width: auto;">
                            <div class="display-block contract-attachment-wrapper img-1">
                                <div class="col-md-6 col-md-offset-3">
                                  <?php $value_id = (isset($item) ? $item->id : '');?>
                                  <?php $avatar = (isset($item) ? $item->avatar : '');?>
                                    <button type="button" class="close" data-id="<?=$value_id?>" data-src="<?=$avatar?>" style="margin-bottom:-20px;color:red;padding-right: 8px;" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <a href="<?php echo (isset($item) && file_exists($item->avatar) ? base_url($item->avatar) : base_url('assets/images/preview-not-available.jpg')) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                        <div class="">
                                            <img style="max-width: 200px;max-height: 300px;" src="<?php echo (isset($item) && file_exists($item->avatar) ? base_url($item->avatar) : base_url('assets/images/preview-not-available.jpg')) ?>">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr/>

                    </div>

                    <script type="text/javascript">
                        function readURL(input, output_img) {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    $(output_img)
                                        .attr('src', e.target.result)
                                        .width(100);
                                };

                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                    </script>
                    <div class="form-group">
                       <label for="code"><?php echo _l('item_code'); ?></label>
                          <?php if(!empty($item)) {
                            $code = $item->code;
                          } else {
                            $code = '';
                          } ?>
                          <input type="text" name="code" id="code" class="form-control code" value="<?=$code?>" placeholder="<?=_l('system_default_string')?>" >
                    </div>
                    <?php
                      $default_name = (isset($item) ? $item->name : "");
                      echo render_input('name', _l('item_name'), $default_name);
                    ?>
                    <?php
                      $default_price = (isset($item) ? $item->price : 0);
                      $_disable='disabled';
                      if(has_permission('items', '', 'price')){
                          $_disable='notdisabled';
                      }
                      echo render_input('price', _l('item_price'), number_format($default_price),'',array('onkeyup'=>"formatNumBerKeyUp(this)",$_disable=>'true'));
                    ?>

                    <?php
                      $default_price = (isset($item) ? $item->price_single : 0);
                       $_disable='disabled';
                      if(has_permission('items', '', 'price_single')){
                          $_disable='notdisabled';
                      }
                        echo render_input('price_single', _l('item_price_single'), number_format($default_price),'',array('onkeyup'=>"formatNumBerKeyUp(this)",$_disable=>'true'));
                    ?>
                    <?php
                      $default_product_features = (isset($item) ? $item->product_features : "");
                     echo render_textarea('product_features','item_product_features',$default_product_features,array(),array(),'','tinymce'); 
                    ?>
                  </div>
                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="checkbox checkbox-primary">
                      <input type="checkbox" id="is_tax" class="is_primary" <?=(isset($item) ? (($item->is_tax == 1) ? "checked" : "") : "")?> name="is_tax"  value="1">
                      <label for="is_tax" data-toggle="tooltip" data-original-title="" title=""><?=_l('ch_is_tax')?></label>
                    </div>
                    <div id="tax_items" class="<?=(isset($item) ? (($item->is_tax == 1) ? "" : "hide") : "hide")?>">
                    <?php
                      $default_tax = (isset($item) ? $item->tax : '');
                      echo render_select('tax', ch_get_all_taxes(), array('id','name'), 'taxes', $default_tax);
                    ?>
                    <?php
                        $default_rate = (isset($item) ? $item->rate : '');
                      echo render_input('rate', _l('tax_add_edit_rate'), $default_rate,'',array(),array('style' => 'display:none;'));
                    ?>
                    </div>

                    <?php 
                    $category_id = (isset($item) ? $item->category_id : "");
                    echo render_select('category_id', $categories, array('id', 'category'), 'ch_categories',$category_id); ?>
                    <?php 
                    $color_id = (isset($item) ? $item->color_id : "");
                    echo render_select('color_id', $color, array('id', 'name'), 'ch_color',$color_id); ?>
                    <?php 
                    $packaging_id = (isset($item) ? $item->packaging_id : "");
                    echo render_select('packaging_id', $packaging, array('id', 'name'), 'ch_packaging',$packaging_id); ?>
                    <?php
                      $units = ch_get_units();
                      $default_unit = (isset($item) ? $item->unit : "");
                      echo render_select('unit', $units, array('unitid','unit'), 'item_unit', $default_unit, array(), array(), '', '', false);
                    ?>
                    <?php
                        $default_minimum = (isset($item) ? $item->minimum_quantity : 0);
                        echo render_input('minimum_quantity', _l('minimum_quantity'), number_format($default_minimum),'',array('onkeyup'=>"formatNumBerKeyUp(this)"));
                    ?>
                    <?php
                        $default_maximum = (isset($item) ? $item->maximum_quantity : 0);
                        echo render_input('maximum_quantity', _l('maximum_quantity'), number_format($default_maximum),'',array('onkeyup'=>"formatNumBerKeyUp(this)"));
                    ?>
                    <?php
                      $groups = ch_get_item_groups();
                      $default_group = (isset($item) ? $item->group_id : "");
                      echo render_select('group_id', $groups, array('id','name'), 'item_group_id', $default_group);
                    ?>
            
                    <?php $date = (isset($item) ? $item->date : ""); ?>
                    <div class="form-group">
                         <label for="date"><?php echo _l('item_date'); ?></label>
                         <div class="input-group">
                          
                            <input type="number" min="0" name="date" class="form-control" value="<?=$date ?>" id="date">
                            <span class="input-group-addon">
                            <?php echo _l('month') ?></span>
                          </div>
                    </div>
                    <?php $warranty = (isset($item) ? $item->warranty : ""); ?>
                    <div class="form-group">
                         <label for="warranty"><?php echo _l('item_warranty'); ?></label>
                         <div class="input-group">
                          
                            <input type="number" min="0" name="warranty" class="form-control" value="<?=$warranty ?>" id=" warranty">
                            <span class="input-group-addon">
                            <?php echo _l('month') ?></span>
                          </div>
                    </div>
 
                    <?php
                      $units = ch_get_brands();
                      $brand_id = (isset($item) ? $item->brand_id : "");
                      echo render_select('brand_id', $units, array('id','name'), 'item_brand', $brand_id, array(), array(), '', '', false);
                    ?>
                    <?php
                      $countries = get_all_countries();
                      $default_contry = (isset($item) ? $item->country_id : "");
                      
                      echo render_select('country_id', $countries, array('country_id','short_name'), 'item_country_id', $default_contry, array(), array(), '', '', false);
                    ?>

                    <?php
                      $default_specification = (isset($item) ? $item->specification : "");
                      echo render_input('specification', _l('item_specification'), $default_specification);
                    ?>

                  </div>
              </div>
              
              <div class="row">
                  <button type="submit" class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                    <?php echo _l( 'submit'); ?>
                </button>
              </div>
            </div>
            <?php echo form_close(); ?>

            <?php if(isset($item)) { ?>
              <style type="text/css">
                .table-invoice-item-price-history tbody tr td:nth-child(2){
                      text-align: right;
                }
                .table-invoice-item-price-history tbody tr td:nth-child(1){
                      text-align: center;
                }
                .table-invoice-item-price-history tbody tr td:nth-child(4){
                      text-align: center;
                }
                .table-invoice-item-price-history tbody tr td:nth-child(3){
                    text-align: right;
                }
                .table-invoice-item-price-single-history tbody tr td:nth-child(2){
                      text-align: right;
                }
                .table-invoice-item-price-single-history tbody tr td:nth-child(3){
                    text-align: right;
                }
                .table-invoice-item-price-single-history tbody tr td:nth-child(1){
                      text-align: center;
                }
                .table-invoice-item-price-single-history tbody tr td:nth-child(4){
                    text-align: center;
                }
                .table-invoice-item-price-single-history thead tr th{
                    text-align: center;
                }
                .table-invoice-item-price-history thead tr th{
                    text-align: center;
                }
    
  
              </style>
            <div role="tabpanel" class="tab-pane" id="item_date">
            
            </div>
            <div role="tabpanel" class="tab-pane" id="item_price_history">
                <div class="row">
                    <div class="col-md-12">
                        <h3><?php echo _l('item_price') ?></h3>
                        <?php render_datatable(array(
                            _l('item_price_date'),
                            _l('item_old_price'),
                            _l('item_new_price'),
                            _l('als_staff'),
                            ),
                            'invoice-item-price-history dont-responsive-table'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3><?php echo _l('item_price_single') ?></h3>
                        <?php render_datatable(array(
                            _l('item_price_date'),
                            _l('item_old_price'),
                            _l('item_new_price'),
                            _l('als_staff'),
                            ),
                            'invoice-item-price-single-history dont-responsive-table '); ?>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="item_files">
              <!--   <?php echo form_open('admin/invoice_items/add_item_attachment',array('class'=>'dropzone mtop30','id'=>'invoice-item-attachment-upload')); ?>
                <?php echo form_close(); ?> -->
                <?php if(get_option('dropbox_app_key') != ''){ ?>
                <hr />
                <div class="text-center">
                    <div id="dropbox-chooser-lead"></div>
                </div>
                <?php } ?>
                <hr />
                <div class="mtop30" id="invoice_item_attachments">
                
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="combo_items">
              <div class="col-md-4">
                <?php 
                    echo render_select('', $items, array('id', 'name','code'), 'Sản phẩm combo','',array(),array(),'items-combo','items-combo');
                ?>
              </div>
              <div class="clearfix mtop10"></div>
              <?php render_datatable(array(
                  _l('STT'),
                  _l('item_name'),
                  _l('item_quantity'),
                  _l('ch_option'),
                ),
                  'combo-items'); 
              ?>
                
            </div>
            <?php } ?>
        </div>
      </div>

        <!-- END PI -->        
  </div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>

</script>
<?php $this->load->view('admin/invoice_items/item_details_js'); ?>
<script>
  $(function(){
        // init_ajax_searchs('color','#color_id')
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
                    for (var t = [], a = e.length, i = 0; i < a; i++) {
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
    <?php if(isset($item)) { ?>
    var notSortableAndSearchableItemColumns = [];
    initDataTable('.table-invoice-item-price-history','<?=admin_url('invoice_items/invoice_item_price_history/' . $item->id)?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[1,'asc']);
    initDataTable('.table-invoice-item-price-single-history','<?=admin_url('invoice_items/invoice_item_price_single_history/' . $item->id)?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[1,'asc']);
    initDataTable('.table-combo-items','<?=admin_url('invoice_items/combo_items/' . $item->id)?>', 'undefined', 'undefined', {},[1,'ASC']);
    <?php } ?>
  });
    appValidateForm($('.items-form'), {
        unit: 'required',
        category_id: 'required',
        minimum_quantity: 'required',
        maximum_quantity: 'required',
        country_id: 'required',
        name: 'required',
        short_name: 'required',
        price: 'required',
        unit: 'required',
        type: 'required',
        code: {
            name: {
                url: admin_url + "misc/items_code_exists",
                type: 'post',
                data: {
                    code: function() {
                        return $('.items-form input[name="code"]').val();
                    },
                    id: function() {
                        return $('body').find('input[name="itemid"]').val();
                    },
                    [csrfData['token_name']] : csrfData['hash']
                },
                remote: 123123123
            }
        },
    });
    $('document').ready(()=>{

        <?php
            if(!isset($item)) {
        ?>
            getRate($('#tax').val());
        <?php
            }
        ?>

        $('#images_product_view').on('click', 'button.close', function(e) {
            var image=$(e.currentTarget).attr('data-src');
            var product_id=$(e.currentTarget).attr('data-id');
            var data={
               [csrfData['token_name']] : csrfData['hash'],
               image: image
              };

              $.post(admin_url + 'invoice_items/delete_image_product/'+product_id, data).done(function(response) 
              {
                if(response=='true')
                {
                  $(e.currentTarget).parent('div').remove();
                }
              });
        });
        $('#avatar_view').on('click', 'button.close', function(e) {
            var image=$(e.currentTarget).attr('data-src');
            var product_id=$(e.currentTarget).attr('data-id');
            var data={
               [csrfData['token_name']] : csrfData['hash'],
               image: image
              };

              $.post(admin_url + 'invoice_items/delete_image_avatar/'+product_id, data).done(function(response) 
              {
                if(response=='true')
                {
                  $(e.currentTarget).parent('div').remove();
                }
              });
        });
    });

    $('body').on('change','#type',function(e){
      var combo=$(this).val();
      if(combo==2)
      {
        $('.li_combo').show();      
      }
      else
      {
        <?php 
          if(!empty($item->id))
          {?>

              if(confirm("<?=_l('ch_note_combo');?>"))
              {
                  
              $('.li_combo').hide();

              }
              else
              {
                $(this).val(2).selectpicker('refresh');
              }

          <?php 
          }
          ?>
      }
    })
    var getRate = (tax_id) => {
        $.get(admin_url + 'invoice_items/get_tax/' + tax_id, (data) => {
            $('#rate').val(data.taxrate);
        }, 'json');
    };
    $('#tax').on('change', ()=>{
        getRate($('#tax').val());
    });
    $('#minimum_quantity').on('change', ()=>{
        var minimum_quantity = $('#minimum_quantity').val();
        if(minimum_quantity < 0)
        {
        $('#maximum_quantity').val(0);
        minimum_quantity = 0;  
        }
        var maximum_quantity = $('#maximum_quantity').val();
        if(minimum_quantity > maximum_quantity)
        {
          $('#maximum_quantity').val(formatNumber(minimum_quantity));
        }
    });
    $('#maximum_quantity').on('change', ()=>{
        var minimum_quantity = unformat_number($('#minimum_quantity').val());
        var maximum_quantity = unformat_number($('#maximum_quantity').val());
        if(maximum_quantity < minimum_quantity)
        {
          alert('<?=_l('ch_wanring_quantity');?>');
          $('#maximum_quantity').val(formatNumber(minimum_quantity));
        }
    });
    function unformat_number(number)
    {
        var _number=0;
        if(number)
        {
            _number=number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2=x2.substr(0,2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };
  function formart_num(id_input)
  {
    key="";
    money=$("#"+id_input).val().replace(/[^\d\.]/g, '');
    a=money.split(".");
    $.each(a , function (index, value){
        key=key+value;
    });
    $("#"+id_input).val(formatNumber(key, '.', '.'));
  }
var table_combo_api;
<?php if(!empty($item)){ ?>

$(document).on('change','select.items-combo',function(e){
  var product_id=$(this).val();
  $.post(admin_url + 'invoice_items/combo_item/<?=$item->id?>',{product_id:product_id,quantity:1,[csrfData['token_name']] : csrfData['hash']}, function(data){
      if(data.success==true)
      {
        alert_float(data.alert_type, data.message);
        $('.items-combo').selectpicker('val','');
        $('.table-combo-items').DataTable().ajax.reload();
      }
    }, 'json');
});
function delete_combo(id)
{
  if(confirm('Bạn có chắc muốn xóa'))
  {
    $.post(admin_url + 'invoice_items/delete_combo/'+id,{[csrfData['token_name']] : csrfData['hash']}, function(data){
      var data = JSON.parse(data);
        alert_float(data.alert_type, data.message);
        $('.table-combo-items').DataTable().ajax.reload();
    })
  } 
}
function update_quantity_combo(id_combo,object_this)
{
  var quantity=$(object_this).val();
   $.post(admin_url + 'invoice_items/update_quantity_combo/'+id_combo,{quantity:quantity,[csrfData['token_name']] : csrfData['hash']}, function(data){
      var data = JSON.parse(data);
        alert_float(data.alert_type, data.message);
        $('.table-combo-items').DataTable().ajax.reload();
    })
}
<?php } ?>

 $(document).on('change', '#is_tax', (e)=>{
    var is_tax = $('#is_tax');
    if(is_tax.is(':checked'))
    {  
        $('#tax_items').removeClass('hide');
    }else{
       $('#tax_items').addClass('hide');
    }

   });
</script>
