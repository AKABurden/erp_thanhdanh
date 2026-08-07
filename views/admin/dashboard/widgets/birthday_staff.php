<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('birthday_staff'); ?>">
  <div class="row">
    <div class="col-md-12">
      <div class="panel_s">
        <div class="panel-body padding-10">
          <div class="padding-10" style="border: 1px solid #dcdcdc;">
            <div class="widget-dragger"></div>
            <p class="padding-5"><?php echo _l('staff_birthday_of_month'); ?></p>
            <hr class="hr-panel-heading-dashboard">
            <table class="table table-bordered table-birthday-staff">
              <thead>
                <tr>
                  <th style="width: 60%;"><?=_l('name')?></th>
                  <th style="width: 40%;"><?=_l('cong_day_birtday')?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $this->db->select('tblstaff.*');
                  $this->db->where('MONTH(birthday)',date('m'));
                  $this->db->limit(10);
                  $get_contact = $this->db->get('tblstaff')->result_array();
                ?>
                <?php if(!$get_contact) { ?>
                  <tr>
                    <td colspan="2"><?=_l('no_data_exists')?></td>
                  </tr>
                <?php } ?>
                <?php foreach ($get_contact as $key => $value) { ?>
                  <tr class="<?=($key%2 == 0) ? 'firts' : 'second'?>">
                    <td style="width: 60%;">
                      <img width="15" src="<?=base_url('uploads/dashboard/birthday.png')?>">
                      <?=$value['firstname']?> <?=($value['lastname']) ? ' '.$value['lastname'] : ''?>
                    </td>
                    <td style="width: 40%;">
                      <?=_dt($value['birthday'])?>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="text-center">
              <a href="<?=admin_url('dashboard/staff_birthday_of_month')?>" class="btn btn-info"><?=_l('more')?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>