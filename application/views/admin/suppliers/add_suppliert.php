<style type="text/css">
.border_ch {
    /*border-bottom: 1px solid #49a1d6;*/
}
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/step_by_step.css')?>">
<div class="modal fade" id="suppliers_add" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $title; ?></h4>
            </div>
            <div class="modal-body">
                <div class="report_debt <?php if($openEdit == 'true'){echo 'hide ';} ?>">
                    <div class="text-center col-md-3 col-xs-3 border-right border-left">
                        <h4 class="bold text-muted votes">
                            <?=number_format($count);?>
                        </h4>
                        <span style="color:red" class="text-danger">
                            <?=_l('ch_total_votes')?>
                        </span>
                    </div>
                    <div class="text-center col-md-3 col-xs-3 border-right">
                        <h4 class="bold text-muted debt">
                            <?=number_format($debt);?>
                        </h4>
                        <span style="color:red" class="text-danger">
                            <?=_l('ch_total_arises')?>
                        </span>
                    </div>
                    <div class="text-center col-md-3 col-xs-3 border-right">
                        <h4 class="bold text-muted payment">
                            <?=number_format($payment);?>
                        </h4>
                        <span style="color:red" class="text-danger">
                            <?=_l('ch_total_payment')?>
                        </span>
                    </div>
                    <div class="text-center col-md-3 col-xs-3 border-right">
                        <h4 class="bold text-muted">
                            <?=number_format($left);?>
                        </h4>
                        <span style="color:red" class="text-danger">
                            <?=_l('ch_total_left')?>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php $value_id = (isset($suppliers) ? $suppliers->id : '');?>
                <?php echo form_hidden('suppliers__id',$value_id); ?>
                <div class="edit <?php if($openEdit != 'true'){echo 'hide ';} ?>">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#supplier_fieldss" onclick="view_submit(1)" aria-controls="supplier_fieldss"
                                role="tab" data-toggle="tab">
                                <?php echo _l( 'ch_suppliers'); ?>
                            </a>
                        </li>
                        <?php if(isset($suppliers)) { ?>
                        <li role="presentation">
                            <a href="#mainstream_items" onclick="view_submit(2)" aria-controls="mainstream_items"
                                role="tab" data-toggle="tab">
                                <?php echo _l('mainstream_items'); ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php echo form_open('admin/suppliers/add_suppliers_v2',array('id'=>'suppliers-add-from')); ?>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane" id="mainstream_items">
                            <?php if(has_permission('suppliers','','create')&&isset($suppliers)){?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="items_combo" class="control-label"><?=_l('items')?></label>
                                    <select class="selectpicker no-margin" data-width="100%" id="items_combo"
                                        data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="clearfix mtop10"></div>
                            <?php render_datatable(array(
                      _l('ch_image'),
                      _l('item_code'),
                      _l('item_name'),
                      _l('ch_color'),
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
                                        <?=_l('address_suppliers')?>
                                    </div>
                                    <div class="wap-left-title bold uppercase event_tab" active-tab="3">
                                        <?=_l('contact')?>
                                    </div>
                                    <?php
                      $customer_custom_fields = false;
                      if(total_rows(db_prefix().'customfields',array('fieldto'=>'suppliers','active'=>1)) > 0 ){$customer_custom_fields = true;?>
                                    <div class="wap-left-title bold uppercase event_tab" active-tab="3">
                                        <?=_l('custom_fields')?>
                                    </div>
                                    <?php } ?>
                                    <?php if(!empty($info_suppliers)){
                      $count = 5;
                      foreach ($info_suppliers as $key => $value) {
                        $info_supplier_value = get_table_where('tblsuppliers_info_detail',array('id_suppliers_info'=>$value['id']));
                            if(!empty($info_supplier_value)){
                      ?>
                                    <div class="wap-left-title bold uppercase event_tab" active-tab="<?=$count?>">
                                        <?=$value['name']?>
                                    </div>
                                    <?php
                      $count++;
                      }}

                    } ?>

                                </div>
                                <div class="wap-right" style="height: 500px;">
                                    <?php if(!empty($info_suppliers)){
                      $count = 5;
                      foreach ($info_suppliers as $key => $value) {
                        $info_supplier_value = get_table_where('tblsuppliers_info_detail',array('id_suppliers_info'=>$value['id']));
                            if(!empty($info_supplier_value)){
                      ?>
                                    <div class="fieldset" role-fieldset="<?=$count?>">
                                        <?php render_hau_suppliert($value['id'],(isset($suppliers) ? $suppliers->id : NULL)) ?>
                                    </div>
                                    <?php
                      $count++;
                      } }

                    } ?>
                                    <div class="fieldset active" role-fieldset="1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label
                                                    for="supplier_code"><?php echo _l('ch_code_suppliers'); ?></label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <?php echo get_option('prefix_supplier') ?>-</span>
                                                    <?php if(!empty($suppliers)) {
                                  $code = $suppliers->code;
                                } else {
                                  $code = '';
                                } ?>
                                                    <input type="text" name="supplier_code" id="supplier_code"
                                                        class="form-control supplier_code" value="<?=$code?>"
                                                        placeholder="<?=_l('system_default_string')?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <?php $value_id = (isset($suppliers) ? $suppliers->id : '');?>
                                            <?php echo form_hidden('id',$value_id); ?>
                                            <?php echo form_hidden('id_ch',$value_id); ?>
                                            <?php $company = (isset($suppliers) ? $suppliers->company : '');?>
                                            <?php echo render_input('company', 'ch_name_suppliers',$company); ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-6">
                                            <?php $phone = (isset($suppliers) ? $suppliers->phone : '');?>
                                            <?php echo render_input('phone', 'clients_phone',$phone); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php $email = (isset($suppliers) ? $suppliers->email : '');?>
                                            <?php echo render_input('email', 'clients_email',$email); ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-6">
                                            <?php $vat = (isset($suppliers) ? $suppliers->vat : '');?>
                                            <?php echo render_input( 'vat', 'clients_vat',$vat); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php $default_currency = (isset($suppliers) ? $suppliers->default_currency : '');?>
                                            <?php echo render_select('default_currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$default_currency); ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-6">
                                            <?php $groups_in = (isset($suppliers) ? $suppliers->groups_in : '');?>
                                            <?php echo render_groups_suppliers_source_select($groups, $groups_in,'ch_groups_suppliers'); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php $debt_limit = (isset($suppliers) ? number_format($suppliers->debt_limit) : 0);?>
                                            <?php echo render_input('debt_limit', 'ch_debt_limit',$debt_limit,'',array('onkeyup'=>"formatNumBerKeyUp(this)")); ?>
                                        </div>
                                        <!-- onkeyup="formatNumBerKeyUp(this)" -->
                                        <div class="clearfix"></div>
                                        <div class="col-md-12">
                                            <?php $note = (isset($suppliers) ? $suppliers->note : '');?>
                                            <?php echo render_textarea('note', 'note',$note); ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <?php if(!isset($suppliers)){?>
                                        <div class="col-md-12">
                                            <div class="checkbox checkbox-primary checkbox-templates">
                                                <input type="checkbox"
                                                    <?=(isset($suppliers) ? ($suppliers->type == 1 ? 'checked' : '') : '')?>
                                                    class="field_client" id="type" name="type" value="1">
                                                <label for="clients_list_company_show">
                                                    <?=_l('tnh_transporters')?>
                                                </label>
                                            </div>
                                        </div>
                                        <?php }?>
                                    </div>
                                    <div class="fieldset" role-fieldset="2">
                                        <div class="append_html">
                                            <?php
                        $countries = get_all_countries();
                        $customer_default_country = get_option('customer_default_country');
                        $selected = (isset($suppliers) ? $suppliers->country : $customer_default_country);
                        echo render_select('country', $countries, array('country_id', array('short_name')), 'lead_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                        ?>
                                            <?php $city = get_table_where('tblprovince', array('countries' => (isset($suppliers) ? $suppliers->country : $customer_default_country))); ?>
                                            <?php $selected = (isset($suppliers) ? $suppliers->city : ''); ?>
                                            <?php echo render_select('city', $city, array('provinceid', 'name'), 'cong_client_city', $selected); ?>
                                            <?php $selected = (isset($suppliers) ? $suppliers->district : ''); ?>
                                            <?php echo render_select('district', (!empty($district) ? $district : []), array('districtid', 'name'), 'cong_client_district', $selected); ?>
                                            <?php $selected = (isset($suppliers) ? $suppliers->ward : ''); ?>
                                            <?php echo render_select('ward', (!empty($ward) ? $ward : []), array('wardid', 'name'), 'cong_client_ward', $selected); ?>
                                            <div class="clearfix"></div>
                                            <div class="col-md-12">
                                                <?php $address = (isset($suppliers) ? $suppliers->address : '');?>
                                                <?php echo render_textarea( 'address', 'client_address',$address); ?>
                                            </div>

                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <div class="fieldset" role-fieldset="3">
                                        <div class="append_html">
                                            <?php
                  if(!empty($suppliers->contacts)){
                    foreach ($suppliers->contacts as $key => $value) {

                    ?>
                                            <div class="col-md-6">
                                                <div class="col-md-12 new_contact_form">
                                                    <div class="remove_contact_panel">
                                                        <a class="remove_html" title="Xóa"><i
                                                                class="fa fa-trash"></i></a>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <?php echo form_hidden("contact[$key][id]",$value['id']); ?>
                                                        <?php echo render_input("contact[$key][name]", 'clients_list_full_name',$value['name']); ?>
                                                        <?php echo render_input("contact[$key][phone]", 'leads_dt_phonenumber',$value['phone']); ?>
                                                        <?php echo render_input("contact[$key][email]", 'client_email',$value['email'],'email'); ?>
                                                    </div>
                                                    <div class="col-md-12">
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
                                                        <input type="checkbox" value="1"
                                                            <?php if($value['receive_email'] == 1){ echo 'checked';} ?>
                                                            name="contact[<?=$key?>][receive_email]"><?=_l('get_email')?>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <input type="checkbox" value="1"
                                                            <?php if($value['main_contact'] == 1){echo 'checked';} ?>
                                                            name="contact[<?=$key?>][main_contact]"><?=_l('key_contact')?>
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
                                                        <a class="remove_html" title="Xóa"><i
                                                                class="fa fa-trash"></i></a>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <?php echo render_input('contact[1][name]', 'clients_list_full_name'); ?>
                                                        <?php echo render_input('contact[1][phone]', 'leads_dt_phonenumber'); ?>
                                                        <?php echo render_input('contact[1][email]', 'client_email','','email'); ?>
                                                    </div>
                                                    <div class="col-md-12">
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
                                                        <input type="checkbox" value="1"
                                                            name="contact[1][receive_email]"><?=_l('get_email')?>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <input type="checkbox" value="1"
                                                            name="contact[1][main_contact]"><?=_l('key_contact')?>
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
                                    <?php if($customer_custom_fields) { ?>
                                    <div class="fieldset" role-fieldset="3">
                                        <?php $value_id = (isset($suppliers) ? $suppliers->id : '');?>
                                        <?php echo render_custom_fields('suppliers',$value_id); ?>
                                    </div>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <button group="submit" type="submit" class="hide" id="submit"
                        class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <div class="view <?php if($openEdit == 'true'){echo 'hide ';} ?>">
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
                                <a href="#mainstream_items_view" aria-controls="mainstream_items_view"
                                    onclick="view_submit(3)" role="tab" data-toggle="tab">
                                    <?php echo _l('mainstream_items'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#history_ask_suppliert" aria-controls="history_ask_suppliert" role="tab"
                                    data-toggle="tab">
                                    <?php echo _l('history_ask_suppliert'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#history_quotes" aria-controls="history_quotes" role="tab" data-toggle="tab">
                                    <?php echo _l('history_quotes'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#history_order" aria-controls="history_order" role="tab" data-toggle="tab">
                                    <?php echo _l('history_order'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="history_quotes">
                                <?php $table_data = array(
                      _l('ID'),
                      _l('ch_code_p'),
                      _l('ch_staff_crate_rfq'),
                      _l('ch_date_p'),
                      _l('invoice_dt_table_heading_status'),
                      _l('ch_items_s'),
                    );
                  $custom_fields = get_custom_fields('supplier_quotes',array('show_on_table'=>1));
                     foreach($custom_fields as $field){
                      array_push($table_data,$field['name']);
                     }
                    render_datatable($table_data,'history_quotes dont-responsive-table');
                  ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="history_order">
                                <?php $table_data = array(
                      _l('ID'),
                      _l('ch_code_p'),
                      _l('ch_date_p'),
                      _l('leads_dt_assigned'),
                      _l('ch_value'),
                      _l('invoice_dt_table_heading_status'),
                      _l('ch_items_s'),
                    );
                    $custom_fields = get_custom_fields('purchase_order',array('show_on_table'=>1));
                     foreach($custom_fields as $field){
                      array_push($table_data,$field['name']);
                     }
                    render_datatable($table_data,'history_order dont-responsive-table');
                  ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="history_ask_suppliert">
                                <?php render_datatable(array(
                      _l('ID'),
                      _l('ch_code_p'),
                      _l('ch_staff_crate_rfq'),
                      _l('ch_date_p'),
                      _l('invoice_dt_table_heading_status'),
                      _l('ch_items_s'),
                    ),
                      'history_ask_suppliert dont-responsive-table'); 
                    ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="mainstream_items_view">
                                <?php render_datatable(array(
                      _l('ch_image'),
                      _l('item_code'),
                      _l('item_name'),
                      _l('ch_color'),
                      _l('ch_option'),
                    ),
                      'productid dont-responsive-table'); 
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
                     render_datatable($table_data,'contacts dont-responsive-table',[],[
                           'data-last-order-identifier' => 'contacts',
                           'data-default-order'         => get_table_last_order('contacts_'),
                     ]);
                     ?>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="supplier_fields">
                                <div id="suppliers_modal_data">
                                    <?php if($type == 2){?>
                                    <!-- hau -->
                                    <div class="col-md-12">
                                        <a onclick="edit()" class="mbot10">
                                            <?php echo _l('edit'); ?>
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
                                    </div>
                                    <?php }?>
                                    <div class="col-md-12">
                                        <div class="wap-left">
                                            <div class="wap-left-title bold uppercase event_tab active" active-tab="1">
                                                <?=_l('lead_info')?>
                                            </div>
                                            <?php if(!empty($info_suppliers)){
                            $tab_temp = 2;
                            foreach ($info_suppliers as $key => $value) {
                              $info_supplier_value = get_table_where('tblsuppliers_info_detail',array('id_suppliers_info'=>$value['id']));
                              if(!empty($info_supplier_value)){
                          ?>
                                            <div class="wap-left-title bold uppercase event_tab"
                                                active-tab="<?=$tab_temp?>">
                                                <?php echo $value['name']; ?>
                                            </div>
                                            <?php $tab_temp++; } } } ?>
                                            <?php $custom_fields = get_custom_fields('suppliers',array()); ?>
                                            <?php if(count($custom_fields) > 0) { ?>
                                            <div class="wap-left-title bold uppercase event_tab"
                                                active-tab="<?=$tab_temp?>">
                                                <?=_l('custom_fields')?>
                                            </div>
                                            <?php $tab_temp++; } ?>
                                            <div class="evaluate_view_left">
                                            </div>
                                        </div>
                                        <div class="wap-right">
                                            <div class="fieldset active" role-fieldset="1">
                                                <div class="wap-content second">
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $suppliers->type != 0 ? _l('tnh_transporters') : '') ?></span>
                                                </div>
                                                <div class="wap-content firt">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('ch_code_suppliers'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo get_option('prefix_supplier') ?>-<?php echo (isset($suppliers) && $suppliers->code != '' ? $suppliers->code : '-') ?></span>
                                                </div>
                                                <div class="wap-content second">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('ch_name_suppliers'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $suppliers->company != '' ? $suppliers->company : '-') ?></span>
                                                </div>
                                                <div class="wap-content firt">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('clients_phone'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $suppliers->phone != '' ? '<a href="tel:'.$suppliers->phone.'">' . $suppliers->phone . '</a>' : '-') ?></span>
                                                </div>
                                                <div class="wap-content second">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('clients_email'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $suppliers->email != '' ? '<a href="mailto:'.$suppliers->email.'">' . $suppliers->email.'</a>' : '-') ?></span>
                                                </div>
                                                <div class="wap-content firt">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('clients_vat'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $suppliers->vat != '' ? $suppliers->vat : '-') ?></span>
                                                </div>
                                                <div class="wap-content second">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('invoice_add_edit_currency'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && !empty($suppliers->default_currency) ? $suppliers->default_currency->name : '-') ?></span>
                                                </div>
                                                <div class="wap-content firt">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('ch_debt_limit'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && !empty($suppliers->debt_limit) ? number_format($suppliers->debt_limit) : '-') ?></span>
                                                </div>
                                                <div class="wap-content second">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('client_address'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $suppliers->address != '' ? $suppliers->address : '-') ?></span>
                                                </div>
                                                <div class="wap-content firt">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('ch_city'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && !empty($suppliers->city) ? ch_getProvince($suppliers->city)->name : '-') ?></span>
                                                </div>
                                                <div class="wap-content second">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('ch_district'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && !empty($suppliers->district) ? ch_getDistrict($suppliers->district)->name : '-') ?></span>
                                                </div>
                                                <div class="wap-content firt">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('ch_ward'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && !empty($suppliers->ward) ? ch_getWard($suppliers->ward)->name : '-') ?></span>
                                                </div>
                                                <div class="wap-content second">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('ch_country'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && !empty($suppliers->country) ? get_country($suppliers->country)->short_name : '-') ?></span>
                                                </div>
                                                <div class="wap-content firt">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo _l('note'); ?>:
                                                    </span>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $suppliers->note != '' ? $suppliers->note : '-') ?></span>
                                                </div>
                                            </div>

                                            <?php 
                            $tab_temp = 2;
                          if(!empty($info_suppliers)){
                            foreach ($info_suppliers as $key => $value) {
                              $info_supplier_value = get_table_where('tblsuppliers_info_detail',array('id_suppliers_info'=>$value['id']));
                              if(!empty($info_supplier_value)){
                          ?>
                                            <div class="fieldset" role-fieldset="<?=$tab_temp?>">
                                                <?php
                            foreach($info_supplier_value as $k => $field) { ?>
                                                <div class="wap-content <?=$k % 2 == 0 ? 'firt' : 'second'?>">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo $field['name']; ?>:
                                                    </span>
                                                    <?php $value = get_value_info_suppliers((isset($suppliers) && isset($suppliers->id) ? $suppliers->id : ''), $field['id'], $field['type_form']); ?>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $value != '' ? $value : '-') ?></span>
                                                </div>
                                                <?php }?>
                                            </div>
                                            <?php $tab_temp++; } } } ?>

                                            <?php $custom_fields = get_custom_fields('suppliers',array()); ?>
                                            <?php if(count($custom_fields) > 0) { ?>
                                            <div class="fieldset" role-fieldset="<?=$tab_temp?>">
                                                <?php
                              $custom_fields = get_custom_fields('suppliers',array());
                              foreach($custom_fields as $k => $field) { ?>
                                                <div class="wap-content <?=$k % 2 == 0 ? 'firt' : 'second'?>">
                                                    <span
                                                        class="text-muted lead-field-heading no-mtop bold"><?php echo $field['name']; ?>:
                                                    </span>
                                                    <?php $value = get_custom_field_value((isset($suppliers) && isset($suppliers->id) ? $suppliers->id : ''), $field['id'], 'suppliers'); ?>
                                                    <span
                                                        class="bold font-medium-xs"><?php echo (isset($suppliers) && $value != '' ? $value : '-') ?></span>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php $tab_temp++; } ?>
                                            <div class="evaluate_view" data-val="<?=$tab_temp?>"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer mtop10">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" id="view_submit"
                    class="bt-view btn btn-info <?php if($openEdit != 'true'){echo 'hide ';} ?>"
                    onclick="sumbit()"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div id="items_view_data"></div>
<script type="text/javascript">
function sumbit() {
    $('#submit').submit();
    $('#view_submit').button('loading');
    setTimeout(function() {
        checkValidateForm_suppliers();
        $('#view_submit').button('reset');
    }, 500);
}
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
                    return $('body').find('input[name="suppliers__id"]').val();
                },
                [csrfData['token_name']]: csrfData['hash']
            }
        }
    },
    email: {
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
                    return $('body').find('input[name="suppliers__id"]').val();
                },
                [csrfData['token_name']]: csrfData['hash']
            }
        }
    },
    vat: {
        // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
        remote: {
            url: admin_url + "misc/suppliers_vat_exists",
            type: 'post',
            data: {
                vat: function() {
                    return $('#suppliers-add-from input[name="vat"]').val();
                },
                userid: function() {
                    return $('body').find('input[name="suppliers__id"]').val();
                },
                [csrfData['token_name']]: csrfData['hash']
            }
        }
    }
}, manage_suppliers);

function manage_suppliers(form) {
    var data = $(form).serialize();
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            if ($.fn.DataTable.isDataTable('.table-suppliers')) {
                $('.table-suppliers').DataTable().ajax.reload();
            }
            alert_float('success', response.message);
        }
        $('#suppliers_add').modal('hide');
        $('#suppliers_id').append('<option selected value="' + response.id + '">' + response.name +
            '</option>');
        $('#suppliers_id').selectpicker('refresh');
        // outsource
        ajaxSelectParamsCallback('#supplies', 'admin/outsource/searchSuppliers', data.id, {
            type: 0
        }, true);
        $('#supplies').val(response.id).trigger('change');
        //
    })
    return false;
}

function edit() {
    $('.edit').removeClass('hide');
    $('.bt-view').removeClass('hide');
    $('.view').addClass('hide');
    $('.report_debt').addClass('hide');
    reSizeHeight();
}
$(function() {
    var CustomersServerParams = {
        'id': '[name="id_ch"]',
    };
    $.each($('._hidden_inputs._filters input'), function() {
        CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

    var tAPI_contact = initDataTable('.table-contacts', admin_url + 'suppliers/table_contacts', [0], [0],
        CustomersServerParams,
        <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);

});
<?php if(!empty($suppliers)){ ?>
$('#items_combo').on('change', function(e) {
    var items_combo = $(this).val();
    var type = $('option:selected', e.currentTarget).attr('data-id');
    $.post(admin_url + 'suppliers/mainstream_items/<?=$suppliers->id?>', {
        id_items: items_combo,
        type: type,
        [csrfData['token_name']]: csrfData['hash']
    }, function(data) {
        if (data.success == true) {
            alert_float(data.alert_type, data.message);
            $('.table-productid_items').DataTable().ajax.reload();
            $('#items_combo').selectpicker('val', '');
        }
    }, 'json');
});
<?php }?>
var tAPI
$(function() {
    <?php if(isset($suppliers)) { ?>
    var notSortableAndSearchableItemColumns = [];
    tAPI = initDataTable('.table-productid',
        '<?=admin_url('suppliers/table_mainstream_items/' . $suppliers->id)?>',
        notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, 'undefined', [1, 'asc']);
    initDataTable('.table-productid_items',
        '<?=admin_url('suppliers/table_mainstream_items/' . $suppliers->id)?>',
        notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, 'undefined', [1, 'asc']);
    initDataTable('.table-history_ask_suppliert',
        '<?=admin_url('suppliers/table_history_ask_suppliert/' . $suppliers->id)?>',
        notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, 'undefined', [1, 'asc']);
    initDataTable('.table-history_quotes', '<?=admin_url('suppliers/table_history_quotes/' . $suppliers->id)?>',
        notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, 'undefined', [1, 'asc']);
    initDataTable('.table-history_order', '<?=admin_url('suppliers/table_history_order/' . $suppliers->id)?>',
        notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, 'undefined', [1, 'asc']);
    <?php }?>

});

function delete_items(id) {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.post(admin_url + 'suppliers/delete_items/' + id, {
            [csrfData['token_name']]: csrfData['hash']
        }, function(data) {
            var data = JSON.parse(data);
            alert_float(data.alert_type, data.message);
            $('.table-productid_items').DataTable().ajax.reload();
        })
    }
}

function view_submit(type) {
    if (type == 1) {
        $('#view_submit').removeClass('hide');
    }
    if (type == 2) {
        tAPI.columns(4).visible(true, true);

        $('#view_submit').addClass('hide');
    }
    if (type == 3) {
        tAPI.columns(4).visible(false, false);
    }
}
$('#country').change(function(e) {
    var id_country = $(this).val();
    $('#city').html("<option></option>").selectpicker('refresh');
    $.post(admin_url + 'clients/get_province', {
        id_country: id_country,
        [csrfData['token_name']]: csrfData['hash']
    }, function(data) {
        data = JSON.parse(data);
        var option = "<option></option>";
        $.each(data, function(i, v) {
            option += '<option value="' + v.provinceid + '">' + v.name + '</option>';
        })
        $('#city').html(option).selectpicker('refresh');
    })
})
$('#city').change(function(e) {
    var id_city = $(this).val();
    $('#district').html("<option></option>").selectpicker('refresh');
    var data = {
        id_province: id_city
    };
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url + 'clients/get_district', data, function(data) {
        data = JSON.parse(data);
        var option = "<option></option>";
        $.each(data, function(i, v) {
            option += '<option value="' + v.districtid + '">' + v.name + '</option>';
        })
        $('#district').html(option).selectpicker('refresh');
    })
})

$('#district').change(function(e) {
    var id_district = $(this).val();
    var data = {
        id_district: id_district
    };
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $('#ward').html("<option></option>").selectpicker('refresh');
    $.post(admin_url + 'clients/get_ward', data, function(data) {
        data = JSON.parse(data);
        var option = "<option></option>";
        $.each(data, function(i, v) {
            option += '<option value="' + v.wardid + '">' + v.name + '</option>';
        })
        $('#ward').html(option).selectpicker('refresh');
    })
})

function int_items_view(id = null) {
    $('#items_view_data').html('');
    $.get(admin_url + 'invoice_items/int_items_view/' + id).done(function(response) {
        $('#items_view_data').html(response);
        $('#items_view').modal({
            show: true,
            backdrop: 'static'
        });
        init_selectpicker();
        init_datepicker();
    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}

$(document).ready(function() {
    init_ajax_searchs('items', '#items_combo');

    function init_ajax_searchs(e, t, a, i) {
        var n = $("body").find(t);
        var h = t;
        if (n.length) {
            var s = {
                ajax: {
                    url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                    data: function() {
                        var t = {
                            [csrfData.token_name]: csrfData.hash
                        };
                        return t.type = e, t.rel_id = "", t.q = "{{{q}}}", t.type_items = -1, void 0 !==
                            a && jQuery.extend(t, a), t
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
                            text: e[i].name,
                            type_items: e[i].type_items
                        };
                        t.push(n)
                    }
                    findItemasdsad(t, h);
                },
                preserveSelectedPosition: "after",
                preserveSelected: !0
            };
            n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker()
                .ajaxSelectPicker(s);

        }
    }
    setTimeout(function() {
        reSizeHeight();
    }, 100);
});
var findItemasdsad = (data, h) => {
    setTimeout(function() {
        $(h).find('option:gt(0)').remove();
        $(h).selectpicker('refresh');
        var count = data.length;
        var html = '';
        $.each(data, function(key, value) {
            if (key == 0) {
                html += '<optgroup label="' + value.text + '">';
            } else if (value.value == 'h') {
                html += '</optgroup>';
                html += '<optgroup label="' + value.text + '">';
            } else {
                html += '<option data-id=' + value.type_items + ' value="' + value.value + '">' +
                    value.text + '</option>';
            }
        });
        html += '</optgroup>';
        $(h).html(html);
        $(h).selectpicker('refresh');
        if (count > 3) {
            $(h).parents().find('.status').addClass('hide');
        } else {
            $(h).parents().find('.status').removeClass('hide');
        }
    }, 1);
};

function reSizeHeight() {
    var Height = $(".wap-left").height();
    Height = Number(Height) - 3;
    var right = document.getElementsByClassName("wap-right");
    right[0].style.height = Height + "px";
}
</script>
<script src="<?=base_url('assets/js/step_by_step.js')?>"></script>