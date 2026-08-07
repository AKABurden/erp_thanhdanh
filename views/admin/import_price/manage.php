<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
             <a class="btn btn-info mright5 test pull-right H_action_button btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><?= lang('search') ?></a>
            <?php if (has_permission('import_price','','create')) { ?>
            <div class="line-sp"></div>
            <a href="<?php echo admin_url('import_price/import' );?>"  id="suppliers_modal" class="btn btn-info mright5 test pull-right H_action_button">
               <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('dt_import_price'); ?></a>
            <?php } ?>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-3 form-group">
                        <?php  echo render_select('price', $data_price,array('id','name_price'),'dt_set_name_supplier'); ?>
                    </div>
                    <div class="col-md-3">
                        <?php  echo render_select('suppliers_name',$price_supplier,array('id','company'),'ch_name_suppliers'); ?>
                    </div>
                </div>
            </div>
            <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('STT'),
                            _l('dt_set_name_supplier'),
                            _l('year'),
                            _l('Mã nhà cung cấp'),
                            _l('ch_name_suppliers'),
                            _l('options')
                        ),'import_price'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="suppliers_view_data"></div>

<?php init_tail(); ?>
<script>

    function view_supplier_detail(id)
    {
   
    $('#suppliers_view_data').html('');
    $.get(admin_url + 'import_price/show_detail_price/'+ id).done(function(response) {
        console.log(response);
    $('#suppliers_view_data').html(response);
    $('#detail_supplier_price').modal({show:true,backdrop:'static'});
    init_selectpicker();
    init_datepicker();
    add_html_evaluate(id);
    }).fail(function(error) {
    var response = JSON.parse(error.responseText);
    alert_float('danger', response.message);
    });    
    }
    var tAPI;

    $(function(){
        appValidateForm($('#id_unit'),{unit:'required'},manage_contract_types);
        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input[name="unit"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
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

    function new_unit(){

        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        $('#unit').val('');
        $('#id_type').attr('action',admin_url+'units/add_unit');
    }
    function edit_type(invoker,id){
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id',id));
        $('#type input[name="unit"]').val(name);
        $('#type').modal('show');
        $('.add-title').addClass('hide');
    }

    $(document).on('click', '.delete-remind', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                tAPI.draw('page');
            }, 'json');
        }
        return false;
    });
    //$(function(){
    //    var CustomersServerParams = {};
    //    $.each($('._hidden_inputs._filters input'),function(){
    //       CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
    //   });
    //    CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
    //
    //     tAPI = initDataTable('.table-import_price', admin_url+'import_price/table', [0], [0], CustomersServerParams,<?php //echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>//);
    //    $('input[name="exclude_inactive"]').on('change',function(){
    //        tAPI.ajax.reload();
    //    });
    //});
    $(function(){
        var CustomersServerParams = {
            'price_name' : '[name="price"]',
            'suppliers_name' : '[name="suppliers_name"]',
        };
        $.each($('._hidden_inputs._filters input'),function(){
            CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });
        CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

         tAPI = initDataTable('.table-import_price', admin_url+'import_price/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>);
        $.each(CustomersServerParams, function(filterIndex, filterItem){
            $(filterItem).on('change', function(){
                tAPI.draw('page');
            });
        });
        $('input[name="exclude_inactive"]').on('change',function(){
            tAPI.ajax.reload();
        });
    });

</script>
