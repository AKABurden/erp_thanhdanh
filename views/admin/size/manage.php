<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
           
            <?php if (is_admin()) { ?>
                <div class="line-sp"></div>
                <a href="#"  onclick="new_size(); return false;" id="suppliers_modal" class="btn btn-info mright5 test pull-right H_action_button">
                   <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                   <?php echo _l('create_add_new'); ?>
                </a>
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
                            _l('name'),
                            _l('options')
                        ),'size'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('size/detail'), array('id'=>'form_size')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('c_update_size'); ?></span>
                    <span class="add-title"><?php echo _l('c_add_size'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'name'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
   // $(function(){
       var CustomersServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

       var tAPI = initDataTable('.table-size', admin_url + 'size/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
   // });
    $(function(){
        appValidateForm($('#form_size'), {name:'required'}, manage_form);
        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input[name="unit"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });
    function manage_form(form) {
        var data = $(form).serialize();
        
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
                alert_float('success',response.message);
            }
            $('.table-size').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }

    function new_size(){

        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        $('#name').val('');
        $('#form_size').attr('action', admin_url + 'size/detail');
    }
    function edit_size(id){
        $('#type').modal('show');
        $('.add-title').addClass('hide');
        $.ajax({
            url : admin_url + 'size/detail/' + id,
            dataType : 'json',
            type : 'get',
        }).done(function(data){
            if(data!="")
            {
                $('#name').val(data.name);
                $('#form_size').prop('action', admin_url + 'size/detail/' + id);
            }
        });
    }

    function delete_size(id = "") {
        if(confirm('Bạn có chắc muốn xóa?')) {
            $.get(admin_url + 'size/delete/' + id, function(data) {
                data = JSON.parse(data);
                alert_float(data.alert_type, data.message);
                tAPI.ajax.reload();
            })
        }
    }

    

</script>
