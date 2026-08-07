<div class="modal fade" id="customer-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title title-customer" id="myModalLabel">
          #<?php echo(isset($client) && $client->userid != '' ? $client->userid : '-') ?> - <?php echo(isset($client) && $client->company != '' ? $client->company : '-') ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="col-md-4 col-xs-12 lead-information-col mbot10">
            <div class="lead-info-heading padding0">
                <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                    <?php echo _l('cong_infomation_client'); ?>
                </h4>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('cong_type_client'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->nameType_client != '' ? $client->nameType_client : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_company_pm'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->company != '' ? $client->company : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_fullname'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->fullname != '' ? $client->fullname : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_note'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->note != '' ? $client->note : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_date_create_company'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->date_create_company != '' ? _d($client->date_create_company) : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('client_vat_number'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->vat != '' ? $client->vat : '-') ?></span>
            </div>

            <div class="lead-info-heading padding0">
                <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                    <?php echo _l('cong_profile_client'); ?>
                </h4>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading no-mtop"><?php echo _l('sex'); ?>: </span>
                <span class="bold font-medium-xs mbot15"><?php echo(isset($client) && $client->gender == 1 ? _l('cong_male') : _l('cong_female')) ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_status_marriage'); ?>: </span>
                <span class="bold font-medium-xs mbot15"><?php echo(isset($client) && $client->nameMarriage != '' ? $client->nameMarriage : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <?php $v = get_table_where('tblcombobox_client',array('id'=>$client->religion),'','row'); ?>
                <span class="text-muted lead-field-heading"><?php echo _l('cong_religion'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->religion != '' ? $v->name : '-') ?></span>
            </div>
        </div>
        <div class="col-md-4 col-xs-12 lead-information-col mbot10">
            <div class="lead-info-heading padding0">
                <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                    <?php echo _l('cong_infomation_client_contact'); ?>
                </h4>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('facebook'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->facebook != '' ? $client->facebook : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('client_phonenumber'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->phonenumber != '' ? $client->phonenumber : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_fax'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->fax != '' ? $client->fax : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('client_address'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->address != '' ? $client->address : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_email_client'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->email_client != '' ? $client->email_client : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('client_website'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->website != '' ? $client->website : '-') ?></span>
            </div>

            <div class="lead-info-heading padding0">
                <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                    <?php echo _l('cong_infomation_profile'); ?>
                </h4>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('cong_client_birtday'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->birtday != '' ? _d($client->birtday) : '-') ?></span>
            </div>
            <div class="wap-content second">
                <?php $v = get_table_where('tblcombobox_client',array('id'=>$client->kt),'','row'); ?>
                <span class="text-muted lead-field-heading"><?php echo _l('KT'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->kt != '' ? $v->name : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <?php $v = get_table_where('tblcombobox_client',array('id'=>$client->dt),'','row'); ?>
                <span class="text-muted lead-field-heading"><?php echo _l('DT'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->dt != '' ? $v->name : '-') ?></span>
            </div>
        </div>
        <div class="col-md-4 col-xs-12 lead-information-col mbot10">
            <div class="lead-info-heading padding0">
                <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                    <?php echo _l('cong_infomation_address'); ?>
                </h4>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('clients_country'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->short_name_countries != '' ? $client->short_name_countries : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_city'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->name_province != '' ? $client->name_province : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_district'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->name_district != '' ? $client->name_district : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_ward'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->name_ward != '' ? $client->name_ward : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('client_state'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->state != '' ? $client->state : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_client_area'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->area != '' ? $client->area : '-') ?></span>
            </div>

            <div class="lead-info-heading padding0">
                <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                    <?php echo _l('cong_infomation_other'); ?>
                </h4>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('cong_client_sources'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->name_sources != '' ? $client->name_sources : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('client_postal_code'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->zip != '' ? $client->zip : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_debt_limit'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->debt_limit != '' ? $client->debt_limit : '-') ?></span>
            </div>
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_debt_limit_day'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->debt_limit != '' ? $client->debt_limit : '-') ?></span>
            </div>
            <div class="wap-content second">
                <span class="text-muted lead-field-heading"><?php echo _l('cong_discount'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->discount != '' ? $client->discount : '-') ?></span>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12">
            <div class="wap-content firt">
                <span class="text-muted lead-field-heading"><?php echo _l('lead_description'); ?>: </span>
                <span class="bold font-medium-xs"><?php echo(isset($client) && $client->description != '' ? $client->description : '-') ?></span>
            </div>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>