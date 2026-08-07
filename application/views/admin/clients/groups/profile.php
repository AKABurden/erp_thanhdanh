<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    ul.dropdown-menu.inner {
        margin-bottom: 0px !important;
    }

    .pd20 {
        padding: 20px;
    }

    .border-ds {
        border: 1px dashed #d0d0d0 !important;
        opacity: .6 !important;
        cursor: pointer;
    }

    .add-contacts:hover {
        opacity: 1 !important;
    }

    .mborder {
        border-bottom: 1px solid #d0d0d0 !important;
    }

    .pborder {
        border: 1px solid #d0d0d0 !important;
    }

    .font-40 {
        font-size: 40px;
    }

    a.removeImg {
        display: inherit;
        top: 0;
        float: left;
    }

    .mbot50 {
        margin-bottom: 50px;
    }
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/step_by_step.css') ?>">
<div class="row mbot50">
    <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?>
    <div class="additional"></div>
    <div class="col-md-12">
        <div class="horizontal-scrollable-tabs" style="min-height: unset">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
                <!--            <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">-->
                <!--               <li role="presentation" class="--><?php //if(!$this->input->get('tab')){echo 'active';}; 
                                                                        ?>
                <!--">-->
                <!--                  <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">-->
                <!--                  --><?php //echo _l( 'customer_profile_details'); 
                                            ?>
                <!--                  </a>-->
                <!--               </li>-->
                <!--                --><?php //if(isset($client)){ 
                                        ?>
                <!--               <li role="presentation">-->
                <!--                  <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">-->
                <!--                  --><?php //echo _l( 'cong_billing_shipping'); 
                                            ?>
                <!--                  </a>-->
                <!--               </li>-->
                <!--               --><?php //hooks()->do_action('after_customer_billing_and_shipping_tab', isset($client) ? $client : false); 
                                        ?>
                <!---->
                <!--               <li role="presentation">-->
                <!--                  <a href="#customer_admins" aria-controls="customer_admins" role="tab" data-toggle="tab">-->
                <!--                  --><?php //echo _l( 'customer_admins' ); 
                                            ?>
                <!--                  </a>-->
                <!--               </li>-->
                <!--               --><?php //hooks()->do_action('after_customer_admins_tab',$client); 
                                        ?>
                <!--               --><?php //} 
                                        ?>

                <!--               --><?php //if(isset($client)){ 
                                        ?>
                <!--                <li role="presentation">-->
                <!--                  <a href="#activity_log" aria-controls="activity_log" role="tab" data-toggle="tab">-->
                <!--                  --><?php //echo _l( 'activity_log_puchases' ); 
                                            ?>
                <!--                  </a>-->
                <!--                </li>-->
                <!--               --><?php //} 
                                        ?>
                <!--            </ul>-->
            </div>
        </div>
        <div class="tab-content">
            <?php hooks()->do_action('after_custom_profile_tab_content', isset($client) ? $client : false); ?>
            <div role="tabpanel" class="tab-pane<?php if (!$this->input->get('tab')) {
                                                    echo ' active';
                                                }; ?>" id="contact_info">
                <div class="customer-view <?php if (!$view) {
                                                echo 'hide';
                                            }; ?>">
                    <?php if (isset($view)) { ?>
                        <input type="hidden" name="VoE" value="1">
                    <?php } ?>
                    <div class="col-md-12">
                        <div class="row">
                            <!--                    <div class="col-md-4 mbot10">-->
                            <!--                      <a class="font-medium-xs" onclick="change_type(); return false;">-->
                            <?//=_l('edit')?>
                            <!--                        <i class="fa fa-pencil-square-o"></i>-->
                            <!--                      </a>-->
                            <!--                    </div>-->
                        </div>
                        <div class="col-md-4 col-xs-12 lead-information-col mbot10">
                            <div class="padding0">
                                <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                                    <?php echo _l('cong_profile_client'); ?>
                                    <a class="colorFFF font-medium-xs pull-right" onclick="change_type(); return false;"><?= _l('edit') ?>
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                </h4>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_customer_orders'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->company != '' ? $dataView->company : '-') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_company_short'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->company_short != '' ? $dataView->company_short : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_date_create_company'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->date_create_company != '' ? _d($dataView->date_create_company) : '-') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('client_vat_number'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->vat != '' ? $dataView->vat : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('client_phonenumber'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->phonenumber != '' ? $dataView->phonenumber : '-') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_email_client'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->email_client != '' ? $dataView->email_client : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_debt_limit'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->debt_limit != '' ? number_format($dataView->debt_limit) : '-') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_debt_limit_day'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->debt_limit_day != '' ? number_format($dataView->debt_limit_day) : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('Công nợ đầu kỳ'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->debt_begin != '' ? number_format($dataView->debt_begin) : '-') ?></span>
                            </div>
                            <div class="wap-content second hide">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_discount'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->discount != '' ? number_format($dataView->discount) : '-') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_discount'); ?>: </span>
                                <?php
                                    $rowDiscount = !empty($dataView) ? $this->site_model->rowDiscount($dataView->discount_id) : '';
                                ?>
                                <span class="bold font-medium-xs"><?php echo (isset($rowDiscount) ? $rowDiscount['name'] : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('client_address'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->address != '' ? $dataView->address : '-') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('clients_country'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->short_name_countries != '' ? $dataView->short_name_countries : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_city'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->name_province != '' ? $dataView->name_province : '-') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_district'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->name_district != '' ? $dataView->name_district : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_ward'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->name_ward != '' ? $dataView->name_ward : '-') ?></span>
                            </div>

                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('cong_client_sources'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (!empty($dataView->name_sources) ? $dataView->name_sources : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('client_website'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->website != '' ? $dataView->website : '-') ?></span>
                            </div>

                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('facebook'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->facebook != '' ? $dataView->facebook : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_quality_standards'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->quality_standards != '' ? $dataView->quality_standards : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_certification'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->certification != '' ? $dataView->certification : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_packing_regulations'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->packing_regulations != '' ? $dataView->packing_regulations : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_price_list_approval'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->price_list_approval != '' ? $dataView->price_list_approval : '-') ?></span>
                            </div> 
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_tm_ck'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) ? ($dataView->tm_ck == 1 ? lang('tnh_tm') :  ($dataView->tm_ck == 2 ? lang('tnh_ck') : '-')) : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('Mã Số XNK'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) ? $dataView->code_xnk : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('VAT'); ?>: </span>
                                <span class="bold font-medium-xs"><?php
                                    $dtVat = !empty($dataView->vat_id) ? get_table_where('tbltaxes',['id' => $dataView->vat_id],'','row_array') : '';
                                    echo (!empty($dtVat) ? $dtVat['name'] : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('Đơn Vị Tiền Thanh Toán'); ?>: </span>
                                <span class="bold font-medium-xs"><?php
                                    $dtCurren = !empty($dataView->currency) ? get_table_where('tblcurrencies',['id' => $dataView->currency],'','row_array') : '';
                                    echo (!empty($dtCurren) ? $dtCurren['name'] : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('Công Thức Chuyển Đổi Tiền'); ?>: </span>
                                <span class="bold font-medium-xs"><?php
                                    $dtCurren = !empty($dataView->currency) ? get_table_where('tblcurrencies',['id' => $dataView->currency],'','row_array') : '';
                                    echo (!empty($dtCurren) ? $dtCurren['name'].'-'.'VNĐ' : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('Loại Hợp Đồng'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) ? $dataView->type_contract : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('Ngày Tái Tục'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) ? (!empty($dataView->date_renewal) ? _dhau($dataView->date_renewal) : '') : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_time_payment'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->time_payment != '' ? $dataView->time_payment : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_bank_account'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->bank_account != '' ? $dataView->bank_account : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_name_account'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->name_account != '' ? $dataView->name_account : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('Địa Chỉ Ngân Hàng'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->address_bank != '' ? $dataView->address_bank : '-') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_contract_number'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->contract_number != '' ? $dataView->contract_number : '-') ?></span>
                            </div>
                            <div class="wap-content firt hide">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_colors'); ?>: </span>
                                <span class="bold font-medium-xs"><?php 
                                    $dtColor = !empty($dataView->colors) ? $this->products_model->rowColors($dataView->colors) : '';
                                    echo (isset($dataView) && $dtColor['name'] != '' ? $dtColor['name'] : '-') ?>
                                </span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('tnh_bale_parameters'); ?>: </span>
                                <span class="bold font-medium-xs"><?php
                                    $dtColor = !empty($dataView->bale_parameters) ? $this->products_model->rowColors($dataView->bale_parameters) : '';
                                    echo (isset($dataView) && $dataView->bale_parameters != '' ? $dataView->bale_parameters : '-') ?>
                                </span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('customer_groups'); ?>: </span>
                                <?php foreach ($dataGroup as $key => $value) { ?>
                                    <span class="bold font-medium-xs"><?php echo (isset($dataGroup) && $value['name_groups'] != '' ? $value['name_groups'] : '-') ?></span>
                                    <?php if (count($dataGroup) > $key + 1) { ?>
                                        <span>, </span>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <!-- <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('Trạng thái khách hàng'); ?>: </span>
                                <span class="bold font-medium-xs">
								    <?=!empty($dataView->status_clients) ? get_table_where('tblstatus_client', ['id' => $dataView->status_clients], '', 'row')->name : '';?>
                                </span>
                            </div> -->
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('tnh_allowed_vat'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo !empty($dataView->allowed_vat) ? lang('yes') : lang('no') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('tnh_separate_guest'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo !empty($dataView->is_separate_guest) ? lang('yes') : lang('no') ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('c_declare_customs'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo !empty($dataView->declare_customs) ? lang('yes') : lang('no') ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('Phân loại khách hàng'); ?>: </span>
                                <span class="bold font-medium-xs"><?php 
                                    $data_status_client = !empty($dataView->status_clients) ? get_table_where('tblstatus_client',['id' => $dataView->status_clients],'','row') : '';
                                    echo !empty($data_status_client) ? $data_status_client->name.'('.$data_status_client->code.')' : ''
                                ?></span>
                            </div>
                            <div class="wap-content firt">
                                <span class="text-muted lead-field-heading"><?php echo _l('Ngày hạch toán'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo !empty($dataView->date_accounting) ? _d($dataView->date_accounting) : '' ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('Tình trạng hoạt động'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo !empty($dataView->status_activity) ? _d($dataView->status_activity) : '' ?></span>
                            </div>

                            <div class="wap-content second hide">
                                <span class="text-muted lead-field-heading no-mtop"><?php echo _l('sex'); ?>: </span>
                                <span class="bold font-medium-xs mbot15"><?php echo (isset($dataView) && $dataView->gender == 1 ? _l('cong_male') : _l('cong_female')) ?></span>
                            </div>
                            <div class="wap-content second">
                                <span class="text-muted lead-field-heading"><?php echo _l('cong_note'); ?>: </span>
                                <span class="bold font-medium-xs"><?php echo (isset($dataView) && $dataView->note != '' ? $dataView->note : '-') ?></span>
                            </div>

                        </div>
                        <!--                  dt-->
                        <div class="col-md-8">
                            <?php if (isset($client)) { ?>
                                <div style="display: flex;justify-content: space-between">
                                    <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
                                        <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
                                    <?php } ?>
                                    <div class="text-center no-margin font-medium-xs bold  uppercase">
                                        <?= _l('Quản trị khách hàng ') ?>
                                    </div>
                                </div>
                                <table class="table dt-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('staff_member'); ?></th>
                                            <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                                            <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
                                                <th><?php echo _l('options'); ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($customer_admins as $c_admin) { ?>
                                            <tr>
                                                <td><a href="<?php echo admin_url('profile/' . $c_admin['staff_id']); ?>">
                                                        <?php echo staff_profile_image($c_admin['staff_id'], array(
                                                            'staff-profile-image-small',
                                                            'mright5'
                                                        ));
                                                        echo get_staff_full_name($c_admin['staff_id']); ?></a>
                                                </td>
                                                <td data-order="<?php echo $c_admin['date_assigned']; ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                                                <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
                                                    <td>
                                                        <a href="<?php echo admin_url('clients/delete_customer_admin/' . $client->userid . '/' . $c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <div style="display: flex;justify-content: space-between">
                                    <button class="btn btn-info mbot20" onclick="ChangeShippingClient('')" type="button"><?= _l('add_sippling') ?></button>
                                    <div class="text-center no-margin font-medium-xs bold  padding10  uppercase">
                                        <?= _l('Thông tin giao hàng ') ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <?php render_datatable(array(
                                            _l('cong_stt'),
                                            _l('cong_shipping'),
                                            _l('ch_contact_shiping'),
                                            _l('ch_elivery_area'),
                                            _l('cong_name_shipping'),
                                            _l('cong_phone'),
                                            _l('shipping_address'),
                                            _l('Quận huyện'),
                                            _l('cong_address_primary'),
                                        ), 'shipping_client'); ?>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                        <!--                  end dt-->
                        <?php include_once(APPPATH . 'views/admin/clients/group_info_client/groups_info_client_view.php'); ?>
                    </div>
                </div>
                <div class="customer-edit <?php if ($view) {
                                                echo 'hide';
                                            }; ?>">
                    <div class="row">
                        <div class="col-md-12<?php if (isset($client) && (!is_empty_customer_company($client->userid) && total_rows(db_prefix() . 'contacts', array('userid' => $client->userid, 'is_primary' => 1)) > 0)) {
                                                    echo '';
                                                } else {
                                                    echo ' hide';
                                                } ?>" id="client-show-primary-contact-wrapper">
                            <div class="checkbox checkbox-info mbot20 no-mtop">
                                <input type="checkbox" name="show_primary_contact" <?php if (isset($client) && $client->show_primary_contact == 1) {
                                                                                        echo ' checked';
                                                                                    } ?> value="1" id="show_primary_contact">
                                <label for="show_primary_contact"><?php echo _l('show_primary_contact', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('payments') . ', ' . _l('credit_notes')); ?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="wap-left">
                                <div class="wap-left-title bold uppercase event_tab title-main active" active-tab="1">
                                    <?= _l('cong_infomation_client') ?>
                                </div>
                                <div class="wap-left-title bold uppercase event_tab title-main" active-tab="2">
                                    <?= _l('cong_infomation_client_contact') ?>
                                </div>

                                <?php $check_custom_fields = false; ?>
                                <?php if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'customers', 'active' => 1)) > 0) {
                                    $check_custom_fields = true; ?>
                                    <div class="wap-left-title bold uppercase event_tab" active-tab="3">
                                        <?= _l('custom_fields') ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($info_group)) { ?>
                                    <?php $dem_temp = 4; //4 là số trường cố định + 1 
                                    ?>
                                    <?php foreach ($info_group as $key => $value) { ?>
                                        <div class="wap-left-title bold uppercase event_tab" active-tab="<?= $dem_temp ?>">
                                            <?= $value['name'] ?>
                                        </div>
                                        <?php $dem_temp++; ?>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="wap-right" style="height: unset !important">
                                <!-- Công bổ sung-->
                                <div class="fieldset active" role-fieldset="1">
                                    <div class="col-md-12">
                                        <div class="align_right">
                                            <a type="button" name="next" class="next action-button">Next</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group input_upload <?= (!empty($client->client_image) ? 'hide' : ''); ?>">
                                            <label for="profile_image" class="profile-image"><?= _l('cong_img_client') ?></label>
                                            <input type="file" name="client_image" class="form-control" id="client_image">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <input type="text" class="hide" id="id_client_ch" value="<?= (isset($client) ? $client->userid : '') ?>">
                                        <?php
                                        if (!empty($client->client_image)) {
                                            if (!empty($client->client_image)) {
                                                $profileImagePath = 'uploads/clients/' . $client->userid . '/thumb_' . $client->client_image;
                                                $url = base_url('download/preview_image?path=' . $profileImagePath);
                                            }
                                        ?>
                                            <a class="removeImg pointer text-danger" name_img="<?= $client->client_image ?>" title="<?= _l('remove_img') ?>" title="<?= _l('remove_image') ?>">X</a>
                                            <img src="<?= $url ?>" class="staff-profile-image-thumb mbot20 imgClient" alt="<?= $client->company ?>">
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <?php $value = (isset($client) ? $client->zcode : ''); ?>
                                            <label for="zcode"><?php echo _l('cong_zcode'); ?></label>
                                            <input type="text" name="zcode" id="zcode" class="form-control zcode" value="<?= $value ?>" placeholder="<?= _l('system_default_string') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <!-- công bổ sung-->
                                        <?php $value = (isset($client) ? $client->company : ''); ?>
                                        <?php $attrs = (isset($client) ? array() : array('autofocus' => true)); ?>
                                        <?php echo render_input('company', 'cong_company_system_lead', $value, 'text', $attrs); ?>
                                        <div id="company_exists_info" class="hide"></div>
                                    </div>

                                    <div class="col-md-6 col-xs-12">
                                        <!-- công bổ sung-->
                                        <?php $value = (isset($client) ? $client->company_short : ''); ?>
                                        <?php echo render_input('company_short', 'cong_company_short', $value, 'text', $attrs); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <!-- công bổ sung-->
                                        <?php $value = (isset($client) ? $client->representative : ''); ?>
                                        <?php echo render_input('representative', 'representative', $value, 'text'); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? _d($client->date_create_company) : ''); ?>
                                        <?php echo render_date_input('date_create_company', 'cong_date_create_company', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php if (get_option('company_requires_vat_number_field') == 1) {
                                            $value = (isset($client) ? $client->vat : '');
                                            echo render_input('vat', 'client_vat_number', $value);
                                        } ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->phonenumber : ''); ?>
                                        <?php echo render_input('phonenumber', 'client_phonenumber', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->email_client : ''); ?>
                                        <?php echo render_input('email_client', 'cong_email_client', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php
                                        $selected = array();
                                        if(isset($branch_client)){
                                            foreach($branch_client as $value){
                                                array_push($selected,$value['branch_id']);
                                            }
                                        }
                                        ?>
                                        <?php echo render_select('branch[]',$branch,array('id','name'),'Chi nhánh',$selected,array('multiple'=>true, 'data-actions-box'=>true),array(),'','',false); ?>

                                    </div>
                                    <div class="hide">
                                        <?php $value = (isset($client) ? $client->fax : ''); ?>
                                        <?php echo render_input('fax', 'cong_client_fax', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->address : ''); ?>
                                        <?php echo render_textarea('address', 'client_address', $value, array('rows' => 3)); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php if ((isset($client) && empty($client->website)) || !isset($client)) {
                                            $value = (isset($client) ? $client->website : '');
                                            echo render_input('website', 'client_website', $value);
                                        } else { ?>

                                            <div class="form-group">
                                                <label for="website"><?php echo _l('client_website'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" name="website" id="website" value="<?php echo $client->website; ?>" class="form-control">
                                                    <div class="input-group-addon">
                                                        <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="hide">
                                        <?php
                                        $selected = (isset($client->religion) ? $client->religion : '');
                                        echo render_select_with_input_group('religion', $religion, ['id', 'name'], 'cong_religion', $selected, '<a href="#" data-toggle="modal" data-type="religion" onclick="SelectType(\'religion\',\'religion\')" data-target="#combobox_client_modal"><i class="fa fa-plus"></i></a>', array('data-actions-box' => true), array(), '', '', false);

                                        ?>
                                        <?php

                                        $selected = (isset($client->marriage) ? $client->marriage : '');
                                        echo render_select_with_input_group('marriage', $marriage, ['id', 'name'], 'cong_status_marriage', $selected, '<a href="#" data-toggle="modal" data-type="marriage" onclick="SelectType(\'marriage\',\'marriage\')" data-target="#combobox_client_modal"><i class="fa fa-plus"></i></a>', array('data-actions-box' => true), array(), '', '', false);

                                        ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->sources : ''); ?>
                                        <?php echo render_select('sources', $sources, array('id', 'name'), 'cong_client_sources', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client->deadline_contract) ? _d($client->deadline_contract) : ''); ?>
                                        <?php echo render_date_input('deadline_contract', 'Thời Hạn Hợp Đồng',    $value); ?>
                                    </div>
                                    <!--Công bổ sung -->
                                    <div class="hide">
                                        <?php $value = (isset($client) ? $client->zip : ''); ?>
                                        <?php echo render_input('zip', 'client_postal_code', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? number_format($client->debt_limit) : ''); ?>
                                        <?php echo render_input('debt_limit', 'cong_debt_limit', $value, 'text', array('onkeyup' => 'formatNumBerKeyUp(this)')); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? number_format($client->debt_limit_day) : ''); ?>
                                        <?php echo render_input('debt_limit_day', 'cong_debt_limit_day', $value, 'text', array('onkeyup' => 'formatNumBerKeyUp(this)')); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? number_format($client->debt_begin) : ''); ?>
                                        <?php echo render_input('debt_begin', 'Công nợ đầu kỳ', $value, 'text', array('onkeyup' => 'formatNumBerKeyUp(this)')); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12 hide">
                                        <?php $value = (isset($client) ? number_format($client->discount) : ''); ?>
                                        <?php echo render_input('discount', 'cong_discount', $value, 'text', array('onkeyup' => 'formatNumBerKeyUp(this)')); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->discount_id : ''); ?>
                                        <?php echo render_select('discount_id', $listDiscount, array('id', 'name'), 'Chiết khấu', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
										<?php $value = (isset($client) ? $client->status_clients : ''); ?>
                                        <?php $StatusClient = get_table_where('tblstatus_client');?>
										<?php echo render_select('status_clients', $StatusClient, array('id', 'name'), 'Trạng thái khách hàng', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <!--end công bổ sung-->
                                        <?php
                                        $selected = array();
                                        if (isset($customer_groups)) {
                                            foreach ($customer_groups as $group) {
                                                array_push($selected, $group['groupid']);
                                            }
                                        }
                                        if (is_admin() || get_option('staff_members_create_inline_customer_groups') == '1') {
                                            echo render_select_with_input_group('groups_in[]', $groups, array('id', 'name'), 'customer_groups', $selected, '<a href="#" data-toggle="modal" data-target="#customer_group_modal"><i class="fa fa-plus"></i></a>', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false);
                                        } else {
                                            echo render_select('groups_in[]', $groups, array('id', 'name'), 'customer_groups', $selected, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false);
                                        }
                                        ?>
                                        <!-- <div class="form-group select-placeholder">
                                            <label for="table_price_id" class="control-label"><?php echo _l('Bảng giá'); ?>
                                            </label>
                                            <select name="table_price_id" id="table_price_id" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""></option>
                                            </select>
                                        </div> -->
                                    </div>
                                    <div class="hide">
                                        <?php if (get_option('disable_language') == 0) { ?>
                                            <div class="form-group select-placeholder">
                                                <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                                                </label>
                                                <select name="default_language" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""><?php echo _l('system_default_string'); ?></option>
                                                    <?php foreach (list_folders(APPPATH . 'language') as $language) {
                                                        $selected = '';
                                                        if (isset($client)) {
                                                            if ($client->default_language == $language) {
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                    ?>
                                                        <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $countries = get_all_countries();
                                        $customer_default_country = get_option('customer_default_country');
                                        $selected = (isset($client) ? $client->country : $customer_default_country);
                                        echo render_select('country', $countries, array('country_id', array('short_name')), 'clients_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                                        ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12 hide">
                                        <?php $value = (isset($client) ? $client->table_price_id : ''); ?>
                                        <?php echo render_select('table_price_id', $table_price_id, array('id', 'name'), 'Bảng giá', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">

                                        <?php $value = (isset($client) ? $client->city : ''); ?>
                                        <?php echo render_select('city', $province, array('provinceid', 'name'), 'cong_client_city', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">

                                        <?php $value = (isset($client) ? $client->district : ''); ?>
                                        <?php echo render_select('district', $district, array('districtid', 'name'), 'cong_client_district', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">

                                        <?php $value = (isset($client) ? $client->ward : ''); ?>
                                        <?php echo render_select('ward', $ward, array('wardid', 'name'), 'cong_client_ward', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->facebook : ''); ?>
                                        <?php echo render_input('facebook', 'facebook', $value); ?>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->quality_standards : ''); ?>
                                        <div class="form-group">
                                            <?= lang('tnh_quality_standards', 'quality_standards') ?>
                                            <input type="text" name="quality_standards" placeholder="<?= lang('tnh_quality_standards') ?>" id="quality_standards" class="form-control quality_standards" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->certification : ''); ?>
                                        <div class="form-group">
                                            <?= lang('tnh_certification', 'certification') ?>
                                            <input type="text" name="certification" placeholder="<?= lang('tnh_certification') ?>" id="certification" class="form-control certification" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->packing_regulations : ''); ?>
                                        <div class="form-group">
                                            <?= lang('tnh_packing_regulations', 'packing_regulations') ?>
                                            <input type="text" name="packing_regulations" placeholder="<?= lang('tnh_packing_regulations') ?>" id="packing_regulations" class="form-control packing_regulations" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->price_list_approval : ''); ?>
                                        <div class="form-group">
                                            <?= lang('tnh_price_list_approval', 'price_list_approval') ?>
                                            <input type="text" name="price_list_approval" placeholder="<?= lang('tnh_price_list_approval') ?>" id="price_list_approval" class="form-control price_list_approval" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6 col-xs-12">
                                                <?php $value = (isset($client) ? $client->tm_ck : ''); ?>
                                                <div class="form-group mbot5">
                                                    <?= lang('tnh_tm_ck', 'tm_ck') ?>
                                                    <div style="display: flex;">
                                                        <div class="radio radio-primary">
                                                            <input type="radio" name="tm_ck" id="tm_ck_1" value="1" <?= !isset($client) ? 'checked="checked"' : ($client->tm_ck == 1 ? 'checked="checked"' : '') ?>>
                                                            <label for="tm_ck_1"><?= lang('tnh_tm') ?></label>
                                                        </div>
                                                        <div class="radio radio-primary" style="margin-top: 10px; margin-left: 10px;">
                                                            <input type="radio" name="tm_ck" id="tm_ck_2" <?= isset($client) && $client->tm_ck == 2 ? 'checked="checked"' : '' ?> value="2">
                                                            <label for="tm_ck_2"><?= lang('tnh_ck') ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php $value = (isset($client) ? $client->time_payment : ''); ?>
                                                <div class="form-group">
                                                    <?= lang('tnh_time_payment', 'time_payment') ?>
                                                    <input type="text" name="time_payment" placeholder="<?= lang('tnh_time_payment') ?>" id="time_payment" class="form-control time_payment number-format" value="<?= $value ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php $value = (isset($client) ? $client->code_xnk : ''); ?>
                                                <div class="form-group">
                                                    <?= lang('Mã Số XNK', 'code_xnk') ?>
                                                    <input type="text" name="code_xnk" placeholder="<?= lang('Mã Số XNK') ?>" id="code_xnk" class="form-control code_xnk" value="<?= $value ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php $value = (isset($client) ? $client->type_contract : ''); ?>
                                                <div class="form-group">
                                                    <?= lang('Loại Hợp Đồng', 'type_contract') ?>
                                                    <input type="text" name="type_contract" placeholder="<?= lang('Loại Hợp Đồng') ?>" id="type_contract" class="form-control type_contract" value="<?= $value ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php $value = (isset($client) ? $client->currency : 0); ?>
                                                <div class="form-group">
                                                    <?= lang('Đơn Vị Tiền Thanh Toán', 'currency') ?>
                                                    <select name="currency" id="currency" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('Đơn Vị Tiền Thanh Toán') ?>" >
                                                        <option value=""></option>
                                                        <?php if(!empty($currencies)): ?>
                                                        <?php foreach ($currencies as $kk => $vv){ ?>
                                                            <option <?= $value == $vv['id'] ? 'selected' : '' ?> value="<?= $vv['id'] ?>"><?= $vv['name'] ?></option>
                                                            <?php } ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php $value = (isset($client) ? $client->vat_id : 0); ?>
                                                <div class="form-group">
                                                    <?= lang('VAT', 'vat_id') ?>
                                                    <select name="vat_id" id="vat_id" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('VAT') ?>" >
                                                        <option value=""></option>
                                                        <?php if(!empty($dtVat)): ?>
                                                            <?php foreach ($dtVat as $kk => $vv){ ?>
                                                                <option <?= $value == $vv['id'] ? 'selected' : '' ?> value="<?= $vv['id'] ?>"><?= $vv['name'] ?></option>
                                                            <?php } ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->bank_account : ''); ?>
                                        <div class="form-group">
                                            <?= lang('tnh_bank_account', 'bank_account') ?>
                                            <input type="text" name="bank_account" placeholder="<?= lang('tnh_bank_account') ?>" id="bank_account" class="form-control bank_account" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->name_account : ''); ?>
                                        <div class="form-group">
                                            <?= lang('tnh_name_account', 'name_account') ?>
                                            <input type="text" name="name_account" placeholder="<?= lang('tnh_name_account') ?>" id="name_account" class="form-control name_account" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->address_bank : ''); ?>
                                        <div class="form-group">
                                            <?= lang('Địa Chỉ Ngân Hàng', 'address_bank') ?>
                                            <input type="text" name="address_bank" placeholder="<?= lang('Địa Chỉ Ngân Hàng') ?>" id="address_bank" class="form-control address_bank" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? (!empty($client->date_renewal) ? _dhau($client->date_renewal) : '') : ''); ?>
                                        <div class="form-group">
                                            <?= lang('Ngày Tái Tục', 'date_renewal') ?>
                                            <input type="text" name="date_renewal" placeholder="<?= lang('Ngày Tái Tục') ?>" id="date_renewal" class="form-control date_renewal datepicker" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->contract_number : ''); ?>
                                        <div class="form-group">
                                            <?= lang('tnh_contract_number', 'contract_number') ?>
                                            <input type="text" name="contract_number" placeholder="<?= lang('tnh_contract_number') ?>" id="contract_number" class="form-control contract_number" value="<?= $value ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php 
                                            $value = (isset($client) ? $client->status_clients : '');
                                            $dtStatusClients = get_table_where('tblstatus_client');
                                        ?>
                                        <div class="form-group">
                                            <?= lang('Phân loại khách hàng', 'status_clients') ?>
                                            <select name="status_clients" id="status_clients" class="form-control selectpicker ajax-select" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('Phân loại khách hàng') ?>" >
                                                <option value=""></option>
                                                <?php if(!empty($dtStatusClients)): ?>
                                                    <?php foreach($dtStatusClients as $key => $value): ?>
                                                        <option <?= (isset($client) && $client->status_clients == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?>(<?= $value['code'] ?>)</option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?= lang('Ngày hạch toán', 'date_accounting') ?>
                                        <?php $value = (isset($client) ? (!empty($client->date_accounting) ? _d($client->date_accounting) : '') : ''); ?>
                                        <div class="form-group">
                                            <input type="text" name="date_accounting" placeholder="<?= lang('Ngày hạch toán') ?>" id="date_accounting" class="form-control date_accounting datepicker" value="<?= $value ?>">    
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?= lang('Tình trạng hoạt động', 'status_activity') ?>
                                        <?php $value = (isset($client) ? (!empty($client->status_activity) ? _d($client->status_activity) : '') : ''); ?>
                                        <div class="form-group">
                                            <input type="text" name="status_activity" placeholder="<?= lang('Tình trạng hoạt động') ?>" id="status_activity" class="form-control status_activity datepicker" value="<?= $value ?>">       
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 hide">
                                        <?php 
                                            $value = (isset($client) ? $client->colors : ''); 
                                            $dtColor = !empty($value) ? $this->products_model->rowColors($value) : '';
                                        ?>
                                        <div class="form-group">
                                            <?= lang('tnh_colors', 'colors') ?>
                                            <select name="colors" id="colors" class="form-control selectpicker ajax-select" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('colors') ?>" >
                                                <option value=""></option>
                                                <?php if(!empty($dtColor)): ?>
                                                    <option selected value="<?= $dtColor['id'] ?>"><?= $dtColor['name'] ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <?php $value = (isset($client) ? $client->bale_parameters : ''); ?>
                                            <div class="form-group">
                                                <?= lang('tnh_bale_parameters', 'bale_parameters') ?>
                                                <textarea name="bale_parameters" id="bale_parameters" class="form-control bale_parameters" rows="3"><?= $value ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->allowed_vat : 0); ?>
                                        <div class="checkbox">
                                            <input type="checkbox" id="allowed_vat" name="allowed_vat" <?= !empty($value) ? 'checked' : '' ?> value="1">
                                            <label for="allowed_vat"><?= _l('tnh_allowed_vat') ?></label>
                                        </div>
                                        <?php $value = (isset($client) ? $client->is_separate_guest : 0); ?>
                                        <div class="checkbox checkbox-info">
                                            <input type="checkbox" id="is_separate_guest" name="is_separate_guest" <?= !empty($value) ? 'checked' : '' ?> value="1">
                                            <label for="is_separate_guest"><?= _l('tnh_separate_guest') ?></label>
                                        </div>
										<?php $value = (isset($client) ? $client->declare_customs : 0); ?>
                                        <div class="checkbox checkbox-info">
                                            <input type="checkbox" id="declare_customs" name="declare_customs" <?= !empty($value) ? 'checked' : '' ?> value="1">
                                            <label for="declare_customs"><?= _l('c_declare_customs') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 hide">
                                        <label class="control-label"><?= _l('sex') ?></label>
                                        <div class="clearfix"></div>
                                        <div class="col-md-6">
                                            <div class="radio">
                                                <input type="radio" id="gender_male" name="gender" value="1" <?= (((isset($client) && $client->gender == 1) || empty($client)) ? 'checked' : '') ?>>
                                                <label for="gender_male"><?= _l('cong_male') ?></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="radio">
                                                <input type="radio" id="gender_female" name="gender" value="2" <?= ((isset($client) && $client->gender == 2) ? 'checked' : '') ?>>
                                                <label for="gender_female"><?= _l('cong_female') ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <?php $value = (isset($client) ? $client->note : ''); ?>
                                        <?php echo render_textarea('note', 'cong_note', $value, array('rows' => 3)); ?>
                                    </div>
                                    <div class="col-md-12 center">
                                        <?php $vip_rating = (isset($client) ? $client->vip_rating : '0'); ?>
                                        <div class="text-center" id="div_rating_client">
                                            <h5><?= _l('cong_vip_rating') ?></h5>
                                            <span class="pointer fa fa-star rating_client <?= (1 <= $vip_rating ? 'checked' : '') ?>" id-star="1" title="<?= _l('cong_1_start') ?>"></span>
                                            <span class="pointer fa fa-star rating_client <?= (2 <= $vip_rating ? 'checked' : '') ?>" id-star="2" title="<?= _l('cong_2_start') ?>"></span>
                                            <span class="pointer fa fa-star rating_client <?= (3 <= $vip_rating ? 'checked' : '') ?>" id-star="3" title="<?= _l('cong_3_start') ?>"></span>
                                            <span class="pointer fa fa-star rating_client <?= (4 <= $vip_rating ? 'checked' : '') ?>" id-star="4" title="<?= _l('cong_4_start') ?>"></span>
                                            <span class="pointer fa fa-star rating_client <?= (5 <= $vip_rating ? 'checked' : '') ?>" id-star="5" title="<?= _l('cong_5_start') ?>"></span>
                                            <input type="hidden" name="vip_rating" id="vip_rating" value="<?= $vip_rating ?>" />
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <!--end công bổ sung-->
                                </div>

                                <div class="fieldset" role-fieldset="2">
                                    <div class="col-md-12">
                                        <div class="align_right">
                                            <a type="button" name="previous" class="previous action-button">Previous</a>
                                            <a type="button" name="next" class="next action-button" <?= ($check_custom_fields == false) ? 'data-stt="2"' : '' ?>>Next</a>
                                        </div>
                                    </div>
                                    <div>
                                        <div id="div_contacts">
                                            <?php if (isset($client->contacts)) {
                                                $i = 0; ?>
                                                <?php foreach ($client->contacts as $key => $value) { ?>
                                                    <div class="col-md-6 items_contact">
                                                        <h5 class="mtop20"><?= _l('cong_contacts') ?></h5>
                                                        <p class="mborder"></p>
                                                        <div class="pborder">
                                                            <div class="text-right">
                                                                <a class="remove_contact_panel pointer text-right text-danger" title="Xóa">
                                                                    <i class="fa fa-trash gf-icon-hover"></i>
                                                                </a>
                                                            </div>
                                                            <div class="col-md-6 mtop10">
                                                                <input type="hidden" id="contacts[<?= $i ?>][id]" name="contacts[<?= $i ?>][id]" value="<?= $value['id'] ?>" />
                                                                <div class="form-group" app-field-wrapper="contacts[<?= $i ?>][firstname]">
                                                                    <label for="contacts[<?= $i ?>][firstname]" class="control-label"> <?= _l('cong_last_firstname') ?></label>
                                                                    <input type="text" name="contacts[<?= $i ?>][firstname]" id="contacts[<?= $i ?>][firstname]" tabindex=<?= (1 * ($i + 1)) ?> class="form-control" autofocus="1" value="<?= $value['firstname'] ?>">
                                                                </div>

                                                                <div class="form-group" app-field-wrapper="contacts[<?= $i ?>][title]">
                                                                    <label for="contacts[<?= $i ?>][title]" class="control-label"> <?= _l('cong_title') ?></label>
                                                                    <input type="text" name="contacts[<?= $i ?>][title]" id="contacts[<?= $i ?>][title]" tabindex=<?= (3 * ($i + 1)) ?> class="form-control" autofocus="1" value="<?= $value['title'] ?>">
                                                                </div>
                                                                <div class="form-group" app-field-wrapper="contacts[<?= $i ?>][phonenumber]">
                                                                    <label for="contacts[<?= $i ?>][phonenumber]" class="control-label"> <?= _l('cong_phonenumber') ?></label>
                                                                    <input type="text" name="contacts[<?= $i ?>][phonenumber]" id="contacts[<?= $i ?>][phonenumber]" tabindex=<?= (5 * ($i + 1)) ?> class="form-control" autofocus="1" value="<?= $value['phonenumber'] ?>">
                                                                </div>

                                                                <div class="client_password_set_wrapper">
                                                                    <label for="password" class="control-label">
                                                                        <?= _l('cong_password') ?>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <input type="password" class="form-control password" name="contacts[<?= $i ?>][password]" autocomplete="false">
                                                                        <span class="input-group-addon">
                                                                            <a href="#" class="show_password" onclick="showPassword('contacts[<?= $i ?>][password]'); return false;"><i class="fa fa-eye"></i></a>
                                                                        </span>
                                                                        <span class="input-group-addon">
                                                                            <a href="#" class="generate_password" onclick="generatePasswordContact(this);return false;"><i class="fa fa-refresh"></i></a>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" id="contacts[<?= $i ?>][is_primary]" class="is_primary" name="contacts[<?= $i ?>][is_primary]" <?= !empty($value['is_primary']) ? 'checked' : '' ?> value="1">
                                                                    <label for="contacts[<?= $i ?>][is_primary]" data-toggle="tooltip"><?= _l('cong_contact_primary') ?></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mtop10">
                                                                <div class="form-group" app-field-wrapper="contacts[<?= $i ?>][email]">
                                                                    <label for="contacts[<?= $i ?>][email]" class="control-label"> <?= _l('cong_email') ?></label>
                                                                    <input type="text" id="contacts[<?= $i ?>][email]" name="contacts[<?= $i ?>][email]" tabindex=<?= (4 * ($i + 1)) ?> class="form-control" autofocus="1" value="<?= $value['email'] ?>">
                                                                </div>
                                                                <div class="form-group" app-field-wrapper="contacts[<?= $i ?>][birtday]">
                                                                    <label for="contacts[<?= $i ?>][birtday]" class="control-label"> <?= _l('cong_birtday') ?></label>
                                                                    <div class="input-group date">
                                                                        <input type="text" id="contacts[<?= $i ?>][birtday]" name="contacts[<?= $i ?>][birtday]" class="datepicker form-control" tabindex="<?= (4 * ($i + 1)) ?>" autofocus="1" value="<?= !empty($value['birtday']) ? _d($value['birtday']) : '' ?>">
                                                                        <div class="input-group-addon">
                                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" app-field-wrapper="contacts[<?= $i ?>][note]">
                                                                    <label for="contacts[<?= $i ?>][note]" class="control-label">Ghi chú</label>
                                                                    <textarea id="contacts[<?= $i ?>][note]" name="contacts[<?= $i ?>][note]" tabindex=<?= (6 * ($i + 1)) ?> class="form-control" rows="4"><?= $value['note'] ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $__is_required_client[$i] = true;
                                                    ?>
                                                <?php ++$i;
                                                } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="pd20 mtop45 border-ds col-md-6 offset-md-6 text-center add-contacts">
                                            <div class="col-md-12 no-padd">
                                                <i class="lnr lnr-users font-40"></i>
                                            </div>
                                            <a>
                                                <i class="gicon-plus mr5 mt3"></i><?= _l('cong_add_contacts') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- công bổ sung-->

                                <div class="fieldset" role-fieldset="3">
                                    <div class="col-md-12">
                                        <div class="align_right">
                                            <a type="button" name="previous" class="previous action-button">Previous</a>
                                            <a type="button" name="next" class="next action-button">Next</a>
                                        </div>
                                    </div>
                                    <?php $rel_id = (isset($client) ? $client->userid : false); ?>
                                    <?php echo render_custom_fields('customers', $rel_id); ?>
                                    <div class="clearfix"></div>
                                </div>
                                <?php include_once(APPPATH . 'views/admin/clients/group_info_client/groups_info_client.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (isset($client)) { ?>
            <?php } ?>
            <!-- activity log-->
            <div role="tabpanel" class="tab-pane" id="activity_log">
                <div class="row">
                    <div class="col-md-12">
                        <div class="activity-container">
                            <?php foreach ($dataLog as $key => $value) { ?>
                                <div class="feed-item">
                                    <div class="activity-text">
                                        <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
                                    </div>
                                    <div class="activity-time">
                                        <?= time_ago($value['date']) ?> <span class="activity-module"><?= _l($value['table_obj']) ?></span>
                                    </div>
                                    <div>
                                        <?= $value['content'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end -->

        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php if (isset($client)) { ?>
    <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
        <div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <?php echo form_open(admin_url('clients/assign_admins/' . $client->userid)); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        $selected = array();
                        foreach ($customer_admins as $c_admin) {
                            array_push($selected, $c_admin['staff_id']);
                        }
                        echo render_select('customer_admins[]', $staff, array('staffid', array('firstname', 'lastname')), '', $selected, array('multiple' => true), array(), '', '', false); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
                <!-- /.modal-content -->
                <?php echo form_close(); ?>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    <?php } ?>
<?php } ?>
<?php $this->load->view('admin/clients/client_group'); ?>
<?php $this->load->view('admin/clients/modals/type_client'); ?>
<?php $this->load->view('admin/clients/modals/combobox_modal'); ?>
<?php $this->load->view('admin/clients/shipping_client'); ?>
<script src="<?= base_url('assets/js/step_by_step.js') ?>"></script>
<script>
    var _is_required_client = <?= !empty($__is_required_client) ? json_encode($__is_required_client) : '[]' ?>;
    $(document).ready(function() {
        initDataTable('.table-shipping_client', admin_url + 'clients/table_shipping/' + customer_id, undefined, undefined, 'undefined', [1, 'desc']);
    });
</script>
<script>
    $(document).ready(function () {
        selectAjax('#colors', false, 'admin/products/searchColors');
    });
</script>