<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
           
            <?php if (is_admin()) { ?>
                <div class="line-sp"></div>
            <a href="#" class="btn btn-info pull-right mleft5 H_action_button" data-toggle="modal" data-target="#groups"><?php echo _l('item_groups'); ?></a>
            <div class="line-sp"></div>
            <a href="#"  onclick="new_video(); return false;" id="suppliers_modal" class="btn btn-info mright5 test pull-right H_action_button">
               <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('create_add_new'); ?></a>
            <?php } ?>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
   
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('id'),
                            _l('Tiêu để video'),
                            _l('Loại video'),
                            _l('options')
                        ),'videos'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('item_groups'); ?>
        </h4>
      </div>
      <div class="modal-body">
          <div class="input-group">
            <input type="text" name="item_group_name" id="item_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
            <span class="input-group-btn">
              <button class="btn btn-info p7" type="button" id="new-item-group-insert"><?php echo _l('new_item_group'); ?></button>
            </span>
          </div>
          <hr />
        <div class="row">
         <div class="container-fluid">
            <?php
              $table_data = [];
              $table_data = array_merge($table_data, array(
                _l('id'),
                _l('item_group_name'),
                _l('ch_option')
                ));
            render_datatable($table_data,'items-groups dont-responsive-table'); ?>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
  </div>
</div>
</div>
</div>
<div id="view_video_html"></div>
<?php init_tail(); ?>
<script>
    $('body').on('click','.edit-item-groups_ch',function(e){
      e.preventDefault();
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit #group_name').val(tr.find('.group_name_plain_text').text());
    });
    $('#new-item-group-insert').on('click',function(){
      var group_name = $('#item_group_name').val();
      if(group_name != ''){
        $.post(admin_url+'videos/add_group',{name:group_name,[csrfData['token_name']] : csrfData['hash']}).done(function(response){
         $('#item_group_name').val('');
        response = JSON.parse(response);
        alert_float(response.alert_type, response.message);
        $('.table-items-groups').DataTable().ajax.reload();
       });
      }
    });
    $('body').on('click','.update-item-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.find('.group_edit #group_id').val();
      var name = tr.find('.group_edit #group_name').val();
      if(name != ''){
        $.post(admin_url+'videos/update_group/'+group_id,{name:name,[csrfData['token_name']] : csrfData['hash']}).done(function(response){
            response = JSON.parse(response);
            alert_float(response.alert_type, response.message);
            $('.table-items-groups').DataTable().ajax.reload();
       });
      }
    });
    function view_init_department(id)
    {
        $('#type').modal('show');
        $('.add-title').addClass('hide');
        $.ajax({
                url : admin_url + 'units/get_row_unit/' + id,
                dataType : 'json',
            })
            .done(function(data){
               if(data!="")
                {
                    $('#unit').val(data.unit);
                    $('#id_unit').prop('action',admin_url+'units/update_unit/'+id);
                }
            });
    }
   $(function(){
        initDataTable('.table-items-groups', admin_url+'videos/table_groups', [0], [0],'undefined',[0,'desc']);
       var CustomersServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

       var tAPI = initDataTable('.table-videos', admin_url+'videos/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
   });
    $(function(){
        appValidateForm($('#id_unit'),{unit:'required'},manage_contract_types);
        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input[name="unit"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });
    $(document).on('click', '.delete-remind_gb', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
              alert_float(response.alert_type, response.message);
                $('.table-items-groups').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });    
    $(document).on('click', '.delete-remind', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
              alert_float(response.alert_type, response.message);
                $('.table-videos').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });
    function manage_contract_types(form) {
        var data = $(form).serialize();
        
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
                alert_float('success',response.message);
            }
            $('.table-units').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }
    function new_video(id) {
        $('#view_video').remove('');
        $.get(admin_url + 'videos/view_video/' + id).done(function (response) {
            $('#view_video_html').html(response);
            $('#view_video').modal('show');
            init_selectpicker();
            // init_editor('.tinymce',{height:200});
        }).fail(function (error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_video', function() {
        $('#view_video_html').html('');
    });    
</script>
