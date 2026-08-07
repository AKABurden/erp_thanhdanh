<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
              <a class="btn btn-info test H_action_button">
                 <?php echo _l('Export excel'); ?></a>
            </div>
            <div class="clearfix"></div>
         </div>
      </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12 hide_btn_options">
        <div class="panel_s">
          <div class="panel-body">
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
                 'name'=>_l('ch_name_suppliers'),
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
  </div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>

<script>
    $(function(){
       var CustomersServerParams = {
       };
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

       var tAPI_contact = initDataTable('.table-contacts', admin_url+'suppliers/table_contacts_all', [0], [0], CustomersServerParams);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI_contact.ajax.reload();
       });
   });
  $(document).on('click', '.delete-remind', function() {
      var r = confirm("<?php echo _l('confirm_action_prompt');?>");
      if (r == false) {
        return false;
      } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            $('.table-suppliers').DataTable().ajax.reload();
            $('.table-contacts').DataTable().ajax.reload();
          }, 'json');
      }
      return false;
    });
</script>
</body>
</html>
