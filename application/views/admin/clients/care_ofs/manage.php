<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <div class="panel-body _buttons">
        <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        <div class="pull-right mright5 H_border">
          <a class="btn btn-info btn-sendemail-list H_action_button" type="button">
            <?=_l('send_email_all')?>
          </a>
        </div>
        <div class="pull-right mright5 H_border">
          <button class="btn btn-info btn-sendsms-list H_action_button" type="button">
            <?=_l('send_sms_all')?>
          </button>
        </div>
    </div>
  </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                   <div class="col-md-2 div_now">
                       <div class="checkbox checkbox-primary">
                           <input type="checkbox" id="date_now" class="date_now" name="date_now" checked value="1">
                           <label for="date_now" data-toggle="tooltip" data-original-title="" title=""><?=_l('now_day')?></label>
                       </div>
                   </div>
                   <div class="col-md-3 div_not_now hide">
                       <?php echo render_date_input('datestart','date_start');?>
                   </div>
                   <div class="col-md-3 div_not_now hide">
                       <?php echo render_date_input('dateend','date_end');?>
                   </div>
               </div>
               <div class="panel-body">
                   <?php render_datatable(array(
                       '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="care_ofs"><label></label></div>',
                       _l('cong_name_client'),
                       _l('cong_company'),
                       _l('cong_day_birtday'),
                       _l('cong_phone'),
                       _l('cong_email'),
                       _l('options'),
                   ),'care_ofs table-bordered'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<div class="modal fade" id="send_email_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title-sendmail"><?php echo _l('send_email_birtday'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/send_toast_email', array('id'=>'send-email-modal')); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('email', 'cong_email'); ?>
                            <?php echo render_input('email_cc', 'cong_email_cc'); ?>
                            <div class="form-group" app-field-wrapper="template_email">
                                <label for="template_email" class="control-label"><?=_l('cong_template_email')?></label>
                                <select id="template_email" name="template_email" class="selectpicker" data-width="100%"  data-live-search="true" tabindex="-98">
                                    <option value=""></option>
                                    <?php foreach($template as $key => $value){?>
                                        <option value="<?=$value['emailtemplateid']?>">
                                            <?=$value['name']?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <?php echo render_input('subject', 'cong_subject'); ?>
                            <?php echo render_textarea('content','','',array(),array(),'','tinymce'); ?>
                            <?php echo form_hidden('id'); ?>
                            <?php echo form_hidden('userid'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button group="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_sending')?>">
                        <?php echo _l('submit'); ?>
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="send_phone_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title-sendmail"><?php echo _l('send_phone_birtday'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/send_toast_phone', array('id'=>'send-phone-modal')); ?>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('phone', 'cong_phone'); ?>
                            <?php echo form_hidden('userid'); ?>
                            <?php echo render_textarea('content_sms','','',array(),array(),'','tinymce'); ?>
                            <?php echo form_hidden('id'); ?>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button group="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_sending')?>">
                        <?php echo _l('submit'); ?>
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="send_list_email_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title-sendmail"><?php echo _l('send_list_email_birtday'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/send_list_toast_email', array('id'=>'send-list_email-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" app-field-wrapper="template_email">
                            <label for="template_email_list" class="control-label"><?=_l('cong_template_email')?></label>
                            <select id="template_email_list" name="template_email_list" class="selectpicker" data-width="100%"  data-live-search="true" tabindex="-98">
                                <option value=""></option>
                                <?php foreach($template as $key => $value){?>
                                    <option value="<?=$value['emailtemplateid']?>">
                                        <?=$value['name']?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php echo render_input('email_cc', 'cong_email_cc'); ?>
                        <?php echo render_input('subject', 'cong_subject'); ?>
                        <?php echo render_textarea('content_list_email','','',array(),array(),'','tinymce'); ?>
                        <div class="hide list_data_send_email">
                            <?php echo form_hidden('list_contact'); ?>
                            <?php echo form_hidden('list_client'); ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_sending')?>">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<script>
   $(function(){
       var CustomersServerParams = {
           'date_start':'[id="datestart"]',
           'date_end':'[id="dateend"]',
           'date_now':'[id="date_now"]:checked'
       };
       initDataTable('.table-care_ofs', admin_url + 'clients/table_care_ofs' , [0], [0], CustomersServerParams, [1, 'desc']);
       $.each(CustomersServerParams, function(filterIndex, filterItem){
           $(filterItem).on('change',function() {
               $('.table-care_ofs').DataTable().ajax.reload();
           });
       });
       $('body').on('change','#date_now',function(e){
           if($(this).prop('checked'))
           {
               $('#date_start').val('');
               $('#date_end').val('');
               $('.div_not_now').addClass('hide');
           }
           else
           {
               $('.div_not_now').removeClass('hide');
           }
       })

   });

   window.addEventListener('load',function(){
       appValidateForm($('#send-email-modal'), {
           email: 'required',
           content: 'required'
       }, manage_send_birtday);

       appValidateForm($('#send-phone-modal'), {
           phone: 'required',
           content_sms: 'required'
       }, manage_send_birtday);

       appValidateForm($('#send-list_email-modal'), {
           content_list_email: 'required'
       }, manage_send_list_birtday);
   });


   function SendEmail(id, type)
   {
       $('#send-email-modal')[0].reset();
       $('#template').selectpicker('refresh').trigger('change');
       if(type == 1)
       {
           var _tr = $('input.data-contact-'+id).parents('tr');
           $('#send_email_modal').find('input[name="id"]').val(id);
           $('#send_email_modal').find('input[name="userid"]').val('');
       }
       else
       {
           var _tr = $('input.data-client-'+id).parents('tr');
           $('#send_email_modal').find('input[name="id"]').val('');
           $('#send_email_modal').find('input[name="userid"]').val(id);
       }
        // var _tr = $(_this).parents('tr');
        var name =_tr.find('td').eq(1).text();
        var email = _tr.find('td').eq(4).text();
        $('#send_email_modal').modal('show');
        $('#send_email_modal').find('#email').val(email);
        $('#send_email_modal').find('#email_cc').val('');

   }

   function SendSMS(id, type)
   {
       if(type == 1)
       {
           var _tr = $('input.data-contact-'+id).parents('tr');
           $('#send_email_modal').find('input[name="id"]').val(id);
           $('#send_email_modal').find('input[name="userid"]').val('');
       }
       else
       {
           var _tr = $('input.data-client-'+id).parents('tr');
           $('#send_email_modal').find('input[name="id"]').val('');
           $('#send_email_modal').find('input[name="userid"]').val(id);
       }
       // var _tr = $(_this).parents('tr');
       var name =_tr.find('td').eq(1).text();
       var phone = _tr.find('td').eq(3).text();
       $('#send_phone_modal').modal('show');
       $('#send_phone_modal').find('#phone').val(phone);

   }

   function manage_send_birtday(form) {
       var data = $(form).serialize();
       var url = form.action;
       $.post(url, data).done(function(response) {
           response = JSON.parse(response);
           if (response.success == true) {
               $('#send_email_modal').modal('hide');
           }
           alert_float(response.alert_type, response.message);
           $('#send_email_modal').find('button[group="submit"]').button('reset');
           $('#send_phone_modal').find('button[group="submit"]').button('reset');
       });
       return false;
   }

   function manage_send_list_birtday(form) {
       var data = $(form).serialize();
       var url = form.action;
       $.post(url, data).done(function(response) {
           response = JSON.parse(response);
           if (response.success == true) {
               $('#send_list_email_modal').modal('hide');
           }
           alert_float(response.alert_type, response.message);
           $('#send_list_email_modal').find('button[group="submit"]').button('reset');
       });
       return false;
   }

   $('body').on('change', '#template_email', function(e){
       var idmessage = $(this).val();
       $.post(admin_url+'clients/get_template/'+idmessage,{[csrfData.token_name]:csrfData.hash}, function(data){
           data = JSON.parse(data);

           $('#send_email_modal').find('#subject').val(data.subject);
           tinymce.get('content').setContent(data.message);
       })
   })

   $('body').on('change', '#template_email_list', function(e){
       var idmessage = $(this).val();
       $.post(admin_url+'clients/get_template/'+idmessage,{[csrfData.token_name]:csrfData.hash}, function(data){
           data = JSON.parse(data);
           $('#send_list_email_modal').find('#subject').val(data.subject);
           tinymce.get('content_list_email').setContent(data.message);
       })
   })

    $('body').on('click','.btn-sendemail-list', function(e){

        $('#send-list_email-modal')[0].reset();
        $('input[name="list_client"]').val('');
        $('input[name="list_contact"]').val('');
        $('#template_email_list').selectpicker('refresh');


        var list_client = $('input.check_client:checked');
        var list_contact = $('input.check_contact:checked');
        $('#template_email_list').trigger('change');
        if(list_client.length > 0 || list_contact.length > 0)
        {
            $('#send_list_email_modal').modal('show');
        }
        else
        {
            alert_float('danger',"<?=_l('not_check_data_send_email')?>")
        }

        var list_data_client = [];
        $.each(list_client, function(i,v)
        {
            list_data_client.push($(v).val());
        })
        list_data_client = list_data_client.join()

        $('input[name="list_client"]').val(list_data_client);
        var list_data_contact = [];
        $.each(list_contact, function(i,v){
            list_data_contact.push($(v).val());
        })
        list_data_contact = list_data_contact.join()
        $('input[name="list_contact"]').val(list_data_contact);
    })
</script>
</body>
</html>
