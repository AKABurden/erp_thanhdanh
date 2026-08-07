<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .width70px{
        width: 70px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="#" class="btn btn-info pull-right H_action_button" onclick="AddItems()">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('cong_button_add_advisory'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                            <?php render_datatable(array(
                                _l('#'),
                                _l('cong_count_userd'),
                                _l('cong_name'),
                                _l('cong_c_color'),
                                _l('cong_background'),
                                _l('options')
                            ),'tagfb dont-responsive-table'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-tagfb', admin_url+'tagfb/table', [0], [0], {}, [1, 'desc']);
        init_color_pickers();
    });
    var cong_you_must_delete = '<?=_l('cong_you_must_delete')?>';
    var InputRequired = {'name':'<?=_l('cong_pls_input_name')?>'};
    $('.table-tagfb').on('draw.dt', function() {
        var invoiceReportsTable = $(this).DataTable();
        init_color_pickers();
    })
    $('body').on('click', '.editTag', function(e){
        var Tr = $(this).parents('tr');
        Tr.find('.p-lable').addClass('hide');
        Tr.find('.input-lable').removeClass('hide');
        $('.table-tagfb').find('tbody').find('tr').not(Tr).find('.NotUpdateTr').trigger('click');
    })

    $('body').on('click', '.NotUpdateTr', function(e){
        var Tr = $(this).parents('tr');
        var inputTd = Tr.find('td');
        $.each(inputTd, function(Key, RValue){
            if($(RValue).find('.input-lable').find('input').length)
            {
                var lable = $(RValue).find('.p-lable').text();
                $(RValue).find('.input-lable').find('input').val(lable);
            }
        })
        Tr.find('.p-lable').removeClass('hide');
        Tr.find('.input-lable').addClass('hide');
    })

    $('body').on('click', '.UpdateTr', function(e){
        var Tr = $(this).parents('tr');
        var id = $(this).attr('id-data');
        var inputTd = Tr.find('td');
        var data = {id:id};

        var button =$(this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');

        $.each(inputTd, function(Key, RValue){
            if($(RValue).find('.input-lable').find('input').length)
            {
                var name = $(RValue).find('.input-lable').find('input').attr('name');
                data[name] = $(RValue).find('.input-lable').find('input').val();
            }
        })
        var dont = false;
        $.each(InputRequired, function(key, input){
            if(data[key] == "")
            {
                alert_float('danger', input);
                Tr.find('input[name="'+key+'"]').focus();
                dont = true;
                return;
            }
        })
        if(dont == true){
            button.button('reset');
            return;
        }
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'tagfb/detail', data, function(data){
            data = JSON.parse(data);
            if(data.success == true)
            {
                $('.table-tagfb').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset');
        });
    })

    function AddItems()
    {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'tagfb/GetAddTr', data, function(data){
           $('.table-tagfb').find('tbody').prepend(data);
            init_color_pickers();
            $('.table-tagfb').find('tbody').find('tr:gt(0)').find('.NotUpdateTr').trigger('click');
        })
    }
    $('body').on('click', '.NotInsertTr', function(e){
        var Tr = $(this).parents('tr').remove();
    })

    $('body').on('click', '.deleteTr', function(e){
        if(confirm(cong_you_must_delete))
        {
           var id =  $(this).attr('id-data');
           if(id != "")
           {
               var button =$(this);
               button.button({loadingText: '<?=_l('cong_please_wait')?>'});
               button.button('loading');
               var data = {id : id};
               if (typeof(csrfData) !== 'undefined') {
                   data[csrfData['token_name']] = csrfData['hash'];
               }
               $.post(admin_url+'tagfb/deleteTag', data, function(data){
                   data = JSON.parse(data);
                   if(data.success)
                   {
                       $('.table-tagfb').DataTable().ajax.reload();
                   }
                   alert_float(data.alert_type, data.message);
               }).always(function() {
                   button.button('reset')
               });
           }
           else
           {
               alert_float('danger', '<?=_l('cong_data_change_not_dele')?>')
           }
        }
    })
</script>


</body>
</html>
