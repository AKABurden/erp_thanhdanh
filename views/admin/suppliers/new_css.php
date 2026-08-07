<style type="text/css">
  .border_ch{
        /*border-bottom: 1px solid #49a1d6;*/
  }
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/step_by_step.css')?>">
<div class="modal fade" id="suppliers_add" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo $title; ?></h4>
         </div>
    <div class="modal-body">
        <div class="edit <?php if($openEdit != 'true'){echo 'hide ';} ?>">
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                  <a href="#supplier_fieldss" onclick="view_submit(1)" aria-controls="supplier_fieldss" role="tab" data-toggle="tab">
                  <?php echo _l( 'ch_suppliers'); ?>
                  </a>
                </li> 
                  <?php
                    $customer_custom_fields = false;
                    if(total_rows(db_prefix().'customfields',array('fieldto'=>'suppliers','active'=>1)) > 0 ){
                         $customer_custom_fields = true;
                  ?>
                  <li role="presentation">
                  <a href="#custom_fields" onclick="view_submit(1)" aria-controls="custom_fields" role="tab" data-toggle="tab">
                  <?php echo hooks()->apply_filters('suppliers_profile_tab_custom_fields_text', _l( 'custom_fields')); ?>
                  </a>
                </li>
                 <?php } ?>
                <?php if(isset($suppliers)) { ?>
                  <li role="presentation">
                    <a href="#mainstream_items" onclick="view_submit(2)" aria-controls="mainstream_items" role="tab" data-toggle="tab">
                      <?php echo _l('mainstream_items'); ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>
                <?php echo form_open('admin/suppliers/add_suppliers',array('id'=>'suppliers-add-from')); ?>
              <div class="tab-content">
                   <?php if($customer_custom_fields) { ?>
                   <div role="tabpanel" class="tab-pane" id="custom_fields">
                    <?php $value_id = (isset($suppliers) ? $suppliers->id : '');?>
                      <?php echo render_custom_fields('suppliers',$value_id); ?>
                   </div>
                   <?php } ?>
              <div role="tabpanel" class="tab-pane" id="mainstream_items">
              <?php if(has_permission('suppliers','','create')&&isset($suppliers)){?>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="items_combo" class="control-label"><?=_l('items')?></label>
                          <select  class="selectpicker no-margin" data-width="100%" id="items_combo" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true" >
                              <option value=""></option>
                              <?php foreach ($items as $item) { ?>
                              <option value="<?php echo $item['id']; ?>" data-id="<?=$item['type_items']?>" data-subtext="">(<?php echo $item['code']; ?>) <?php echo $item['name']; ?></option>
                              <?php 
                              } ?>
                          </select>
                      </div>
                  </div>
             <?php } ?> 
                  <div class="clearfix mtop10"></div>
                 <?php render_datatable(array(
                      _l('item_code'),
                      _l('item_name'),
                      _l('ch_option'),
                    ),
                      'productid_items'); 
                  ?>
                
              </div>

              <div role="tabpanel" class="tab-pane active" id="supplier_fieldss">
                <div class="col-md-12">
                  <div class="wap-left">
                    <div class="wap-left-title bold uppercase event_tab active" active-tab="1">
                      <?=_l('ch_information')?>
                    </div>
                    <div class="wap-left-title bold uppercase event_tab" active-tab="2">
                      <?=_l('contact')?>
                    </div>
                  </div>
                  <div class="wap-right">
                    <div class="fieldset active" role-fieldset="1">
                        <div class="form-group">
                           <label for="supplier_code"><?php echo _l('ch_code_suppliers'); ?></label>
                           <div class="input-group">
                            <span class="input-group-addon">
                              <?php echo get_option('prefix_supplier') ?>-</span>
                              <?php if(!empty($suppliers)) {
                                $code = $suppliers->code;
                              } else {
                                $code = '';
                              } ?>
                              <input type="text" name="supplier_code" id="supplier_code" class="form-control supplier_code" value="<?=$code?>" placeholder="Mặc định hệ thống" >
                            </div>
                        </div> 
                          <?php $value_id = (isset($suppliers) ? $suppliers->id : '');?>
                          <?php echo form_hidden('id',$value_id); ?>
                          <?php $company = (isset($suppliers) ? $suppliers->company : '');?>
                          <?php echo render_input('company', 'ch_name_suppliers',$company); ?>
                          <?php $phone = (isset($suppliers) ? $suppliers->phone : '');?>
                          <?php echo render_input('phone', 'clients_phone',$phone); ?>
                          <?php $email = (isset($suppliers) ? $suppliers->email : '');?>
                          <?php echo render_input('email', 'clients_email',$email); ?>

                        <?php $vat = (isset($suppliers) ? $suppliers->vat : '');?>
                          <?php echo render_input( 'vat', 'clients_vat',$vat); ?>
                          <?php $default_currency = (isset($suppliers) ? $suppliers->default_currency : '');?>
                            <?php 
                               echo render_select('default_currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$default_currency); ?>
                            <?php $groups_in = (isset($suppliers) ? $suppliers->groups_in : '');?>   
                              <?php
                                 echo render_groups_suppliers_source_select($groups, $groups_in,'ch_groups_suppliers');
                              ?>
                          <?php $address = (isset($suppliers) ? $suppliers->address : '');?>    
                          <?php echo render_textarea( 'address', 'client_address',$address); ?>

                        <?php $note = (isset($suppliers) ? $suppliers->note : '');?> 
                        <?php echo render_textarea('note', 'note',$note); ?>
                    </div>

              <div class="fieldset" role-fieldset="2">
                <div class="append_html">
                  <?php
                  if(!empty($suppliers->contacts)){
                    foreach ($suppliers->contacts as $key => $value) {

                    ?>
                    <div class="col-md-6">
                      <div class="col-md-12 new_contact_form"> 
                        <div class="remove_contact_panel">
                          <a class="remove_html" title="Xóa"><i class="fa fa-trash"></i></a>
                        </div>
                        <div class="col-md-6">
                            <?php echo form_hidden("contact[$key][id]",$value['id']); ?>
                            <?php echo render_input("contact[$key][name]", 'clients_list_full_name',$value['name']); ?>
                            <?php echo render_input("contact[$key][phone]", 'leads_dt_phonenumber',$value['phone']); ?>
                            <?php echo render_input("contact[$key][email]", 'client_email',$value['email'],'email'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input("contact[$key][address]", 'settings_sales_address',$value['address']); ?>
                            <?php echo render_date_input("contact[$key][birthday]", 'birthday',_d($value['birthday'])); ?>
                            <?php $sex = array(
                                array(
                                    'id'=>1,
                                    'name'=>'Nam',
                                ),
                                array(
                                    'id'=>2,
                                    'name'=>'Nữ',
                                ),
                                array(
                                    'id'=>3,
                                    'name'=>'Khác',
                                ),
                            ); ?>
                            <?php echo render_select("contact[$key][sex]",$sex,array('id','name'), 'sex',$value['sex']); ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_input("contact[$key][note]", 'invoice_note',$value['note']); ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="checkbox" value="1" <?php if($value['receive_email'] == 1){ echo 'checked';} ?> name="contact[<?=$key?>][receive_email]"><?=_l('get_email')?>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="checkbox" value="1" <?php if($value['main_contact'] == 1){echo 'checked';} ?> name="contact[<?=$key?>][main_contact]"><?=_l('key_contact')?>
                        </div>
                      </div>
                  </div>
                    <?php }
                  }else
                  {
                   ?>
                  <div class="col-md-6">
                      <div class="col-md-12 new_contact_form"> 
                        <div class="remove_contact_panel">
                          <a class="remove_html" title="Xóa"><i class="fa fa-trash"></i></a>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('contact[1][name]', 'clients_list_full_name'); ?>
                            <?php echo render_input('contact[1][phone]', 'leads_dt_phonenumber'); ?>
                            <?php echo render_input('contact[1][email]', 'client_email','','email'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('contact[1][address]', 'settings_sales_address'); ?>
                            <?php echo render_date_input('contact[1][birthday]', 'birthday'); ?>
                            <?php $sex = array(
                                array(
                                    'id'=>1,
                                    'name'=>'Nam',
                                ),
                                array(
                                    'id'=>2,
                                    'name'=>'Nữ',
                                ),
                                array(
                                    'id'=>3,
                                    'name'=>'Khác',
                                ),
                            ); ?>
                            <?php echo render_select('contact[1][sex]',$sex,array('id','name'), 'sex'); ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_input('contact[1][note]', 'invoice_note'); ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="checkbox" value="1" name="contact[1][receive_email]"><?=_l('get_email')?>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="checkbox" value="1" name="contact[1][main_contact]"><?=_l('key_contact')?>
                        </div>
                      </div>
                  </div>
                <?php }?>
                </div>

              <div class="col-md-6">
                <div class="col-md-12 btn_add_contact"> 
                  <div class="add_new_contact" onclick="add_contact_view();return false;"> 
                    <div> 
                      <span><i class="fa fa-user-plus"></i></span>
                      <p><?=_l('add_contact')?></p>
                    </div> 
                  </div> 
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            </div>

          </div>
          <button group="submit" type="submit" class="hide" id="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        </div>
        <div  class="view <?php if($openEdit == 'true'){echo 'hide ';} ?>">
            <div class="modal-body">
            <ul class="nav nav-tabs" role="tablist">
               <li role="presentation" class="active">
                <a href="#supplier_fields" aria-controls="supplier_fields" role="tab" data-toggle="tab">
                <?php echo _l( 'staff_profile_string'); ?>
                </a>
              </li>      
               <li role="presentation">
                <a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">
                <?php echo _l( 'customer_contacts'); ?>
                </a>
              </li>
              <li role="presentation">
                  <a href="#mainstream_items_view" aria-controls="mainstream_items_view" onclick="view_submit(3)"  role="tab" data-toggle="tab">
                  <?php echo _l('mainstream_items'); ?>
                  </a>
              </li>     
            </ul>
              <div class="tab-content">
                  <div role="tabpanel" class="tab-pane" id="mainstream_items_view">
                    <?php render_datatable(array(
                      _l('item_code'),
                      _l('item_name'),
                      _l('ch_option'),
                    ),
                      'productid'); 
                    ?>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="contact">
                    <?php
                     $table_data = array();
                     $_table_data = array(
                       array(
                         'name'=>_l('the_number_sign'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
                        ),
                         array(
                         'name'=>_l('name'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                         array(
                         'name'=>_l('company_primary_email'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-primary-contact')
                        ),
                         array(
                         'name'=>_l('staff_add_edit_phonenumber'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-primary-contact-email')
                        ),
                         array(
                         'name'=>_l('birthday'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-primary-contact-email')
                        )
                      );
                     foreach($_table_data as $_t){
                      array_push($table_data,$_t);
                     }
                     render_datatable($table_data,'contacts',[],[
                           'data-last-order-identifier' => 'contacts',
                           'data-default-order'         => get_table_last_order('contacts_'),
                     ]);
                     ?>
                  </div>
                  <div role="tabpanel" class="tab-pane active" id="supplier_fields">
                    <?php if($type == 2){?>
                    <a href="#" onclick="edit()">
                     <?php echo _l('edit'); ?>
                     <i class="fa fa-pencil-square-o"></i>
                    </a>
                   <?php }?>
                    
                    <div class="clearfix"></div>
                    <br>
                        <div class="col-md-6 col-xs-12 lead-information-col">
                          <div class="lead-info-heading">
                             <h4 class="no-margin font-medium-xs bold">
                                <?php echo _l('lead_info'); ?>
                             </h4>
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('ch_code_suppliers'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo get_option('prefix_supplier') ?>-<?php echo (isset($suppliers) && $suppliers->code != '' ? $suppliers->code : '-') ?></strong>
                            </span> 
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('ch_name_suppliers'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $suppliers->company != '' ? $suppliers->company : '-') ?></strong>
                            </span> 
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('clients_phone'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $suppliers->phone != '' ? '<a href="tel:'.$suppliers->phone.'">' . $suppliers->phone . '</a>' : '-') ?></strong>
                            </span> 
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('clients_email'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $suppliers->email != '' ? '<a href="mailto:'.$suppliers->email.'">' . $suppliers->email.'</a>' : '-') ?></strong>
                            </span> 
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('clients_vat'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $suppliers->vat != '' ? $suppliers->vat : '-') ?></strong>
                            </span> 
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('invoice_add_edit_currency'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $suppliers->default_currency != '' ? $suppliers->default_currency : '-') ?></strong>
                            </span> 
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('client_address'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $suppliers->address != '' ? $suppliers->address : '-') ?></strong>
                            </span> 
                          </div>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo _l('note'); ?>:</label> 
                            <span>
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $suppliers->note != '' ? $suppliers->note : '-') ?></strong>
                            </span> 
                          </div>
                       </div>
                       <div class="col-md-6 col-xs-12 lead-information-col">
                          <div class="lead-info-heading">
                             <h4 class="no-margin font-medium-xs bold">
                                <?php echo _l('custom_fields'); ?>
                             </h4>
                          </div>
                          <?php $custom_fields = get_table_custom_fields('suppliers'); ?>
                          <?php
                          $custom_fields = get_custom_fields('suppliers',array('show_on_table'=>1));
                           foreach($custom_fields as $field){
                            ?>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo $field['name']; ?>:</label> 
                            <span>
                              <?php $value = get_custom_field_value((isset($suppliers) && isset($suppliers->id) ? $suppliers->id : ''), $field['id'], 'suppliers'); ?> 
                              <strong class="ng-binding"><?php echo (isset($suppliers) && $value != '' ? $value : '-') ?></strong>
                            </span> 
                          </div>
                          <?php 
                           }
                           ?>
                        </div>
                  </div>
              </div>
           </div>
         </div>
        </div>
         <div class="clearfix"></div>
         <div class="modal-footer mtop10">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button group="submit" id="view_submit" class="bt-view btn btn-info <?php if($openEdit != 'true'){echo 'hide ';} ?>" onclick="sumbit()" ><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<script type="text/javascript">
    function sumbit() {
      $('#submit').submit();
      checkValidateForm();
    }
    <?php if($openEdit == 'true'){?>
    appValidateForm($('#suppliers-add-from'), {
       company: 'required',
       phone: 'required',
       groups_in: 'required',
       supplier_code: {
            remote: {
                url: admin_url + "misc/suppliers_code_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#suppliers-add-from input[name="supplier_code"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="id"]').val();
                    },
                    [csrfData['token_name']] : csrfData['hash']
                }
            }
        },
       email: {
            required: true,
            email: true,
            // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
            remote: {
                url: admin_url + "misc/suppliers_email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#suppliers-add-from input[name="email"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="id"]').val();
                    },
                    [csrfData['token_name']] : csrfData['hash']
                }
            }
        },
       vat: {
            required: true,
            // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
            remote: {
                url: admin_url + "misc/suppliers_vat_exists",
                type: 'post',
                data: {
                    vat: function() {
                        return $('#suppliers-add-from input[name="vat"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="id"]').val();
                    },
                    [csrfData['token_name']] : csrfData['hash']
                }
            }
        }
    },manage_suppliers);
<?php } ?>
    function edit() {
      $('.edit').removeClass('hide');
      $('.bt-view').removeClass('hide');
      $('.view').addClass('hide');
    }
    $(function(){
       var CustomersServerParams = {
        'id':'[name="id"]',
       };
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

       var tAPI_contact = initDataTable('.table-contacts', admin_url+'suppliers/table_contacts', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
  
   });
<?php if(!empty($suppliers)){ ?>
$(document).on('change','#items_combo',function(e){
  var items_combo=$(this).val();
 var type = $('option:selected', e.currentTarget).attr('data-id');
  $.post(admin_url + 'suppliers/mainstream_items/<?=$suppliers->id?>',{id_items:items_combo,type:type,[csrfData['token_name']] : csrfData['hash']}, function(data){
      if(data.success==true)
      {
        alert_float(data.alert_type, data.message);
        $('.table-productid_items').DataTable().ajax.reload();
        $('#items_combo').selectpicker('val','');
      }
    }, 'json');
});
<?php }?>
var tAPI
  $(function(){
    <?php if(isset($suppliers)) { ?>
    var notSortableAndSearchableItemColumns = [];
    tAPI = initDataTable('.table-productid','<?=admin_url('suppliers/table_mainstream_items/' . $suppliers->id)?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[1,'asc']);
      initDataTable('.table-productid_items','<?=admin_url('suppliers/table_mainstream_items/' . $suppliers->id)?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[1,'asc']);
    <?php }?>  

});
  function delete_items(id)
      { 
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
      if (r == false) {
        return false;
      } else {
         $.post(admin_url + 'suppliers/delete_items/'+id,{[csrfData['token_name']] : csrfData['hash']}, function(data){
        var data = JSON.parse(data);
          alert_float(data.alert_type, data.message);
          $('.table-productid_items').DataTable().ajax.reload();
      })
       }
  } 
    function view_submit(type) {
    if(type == 1)
    {
      $('#view_submit').removeClass('hide');
    }
    if(type == 2)
    {
      tAPI.columns(2).visible(true, true);

      $('#view_submit').addClass('hide');
    }
    if(type == 3)
    {
      tAPI.columns(2).visible(false, false);
    }
  }
</script>
<script src="<?=base_url('assets/js/step_by_step.js')?>"></script>