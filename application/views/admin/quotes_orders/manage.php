<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .progressbar:not(.initli) {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }
    .progressbar li span{
        font-size: 11px;
    }
    .progressbar li:not(.initli) {
        list-style-type: none;
        width: 100px;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        /*text-transform: uppercase;*/
        color: #7d7d7d;
        z-index: 0;
    }
    .progressbar li:not(.initli):before {
        width: 10px;
        height: 10px;
        content: ' ';
        counter-increment: step;
        line-height: 51px;
        border: 5px solid #7d7d7d;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: white;
    }
    .progressbar li:not(.initli):after {
        width: 100%!important;
        height: 2px!important;
        content: ''!important;
        position: absolute!important;
        background-color: #7d7d7d!important;
        top: 4px!important;
        left: -50%!important;
        z-index: -1!important;
    }
    .progressbar li:first-child:after {
        content: none;
        display: none;
    }
    .progressbar li.active:not(.initli) {
        color: green;
    }
    .progressbar li.active:not(.initli):before {
        border-color: #55b776;
    }
    .progressbar li.cancel:before {
        border-color: red;
    }
    .progressbar li.active + li:after {
        background-color: #55b776!important;
    }
    .font11
    {
        font-size: 11px;
    }
    .btn-info.active, .btn-info:active{
        background-color: #094865;
    }
    .table-orders th, .table-orders td { white-space: nowrap; }
    .mw600{
        min-width: 600px;
    }
    .li_pad10{
        white-space: normal;
        padding-left: 10px;
    }
    .CRa{
        color: #55b776;
    }
    .progressbar_img{
        text-align: center!important;
        display: flex;
        flex-direction: row;
        justify-content: center;
    }
    ul.progressbar_img li {
        width: 100px;
        float: left;
    }
    .CRwa{
        color: red;
    }
    .initli{
        width: 100px;
    }
    tr.bg-warning {
        background-color: #fcf8e3;
    }
    tr.bg-dd {
        background-color: #dddddd;
    }
    .initli .status_orders
    {
        color:red;
    }

</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <?php if (has_permission('orders','','create')) { ?>
            <a href="<?=admin_url('quotes_orders/detail')?>"  class="btn btn-info mright5 test pull-right H_action_button">
               <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('create_add_new'); ?></a>
            <?php } ?>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
    <div class="content">
        <div class="row">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="horizontal-scrollable-tabs">
                        <div class="scroller scroller-left arrow-left disabled" style="display: block;"><i class="fa fa-angle-left"></i></div>
                        <div class="scroller scroller-right arrow-right" style="display: block;"><i class="fa fa-angle-right"></i></div>
                        <div class="horizontal-tabs">
                            <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                <li class="active">
                                    <a class="H_filter" data-id="">
                                        <?=_l('cong_all')?> <b class="filter_"></b>
                                    </a>
                                </li>
                                <li>
                                    <a class="H_filter" data-id="0">
                                        <?=_l('cong_new_create')?> <b class="filter_0"></b>
                                    </a>
                                </li>
                                <li>
                                    <a class="H_filter" data-id="1">
                                        <?=_l('cong_have_create_orders')?> <b class="filter_1"></b>
                                    </a>
                                </li>
                                <li>
                                    <a class="H_filter" data-id="2">
                                        <?=_l('cong_have_cancel_quotes_orders')?> <b class="filter_2"></b>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                    <div class="clearfix mtop20"></div>
                    <?php
                        $table_data = array(
                            _l('cong_code'),
                            _l('cong_client'),
                            _l('cong_date'),
                            _l('cong_assigned'),
                            _l('cong_status'),
                            _l('cong_date_create'),
                            _l('cong_create_by'),
                            _l('cong_num_item'),
                            _l('cong_cost_trans'),
                            _l('cong_total_orders_'),
                        );
                        render_datatable($table_data, 'quotes_orders table-bordered');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>

$(function(){
    var CustomersServerParams = {
      'filterStatus' : '[name="filterStatus"]',
    };

    var tAPI = initDataTable('.table-quotes_orders', admin_url+'quotes_orders/table', [], [], CustomersServerParams, ['0', 'desc']);

    $.each(CustomersServerParams, function(filterIndex, filterItem){
          $(filterItem).on('change', function(){
                tAPI.ajax.reload();
          });
    });
});

$('.table-quotes_orders').on('draw.dt',function(){
    var expenseReportsTable = $(this).DataTable();
    var total = expenseReportsTable.ajax.json().total;
    var sumtotal = 0;
    $.each(total, function(i, v){
        $('.filter_'+i).html('('+formatNumber(v)+')');
        if(v > 0)
        {
            sumtotal++;
        }
    })
    $('.filter_').html('('+formatNumber(sumtotal)+')');
})

$('body').on('click', '.H_filter', function(e){
    $('.H_filter').parent('li').removeClass('active');
    $(this).parent('li').addClass('active');
    $('input[name="filterStatus"]').val($(this).attr('data-id'));
    $('.table-quotes_orders').DataTable().ajax.reload();
})

function CancelOrders(id){
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    data['id'] = id;
    $.post(admin_url+'quotes_orders/CancelStatus', data, function(data){
        data = JSON.parse(data);
        if(data.success)
        {
            $('.table-quotes_orders').DataTable().ajax.reload();
        }
        alert_float(data.alert_type, data.message);
    })
}



function restore_orders(id = "")
{
    if($.isNumeric(id))
    {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        $.post(admin_url+'quotes_orders/restore_orders', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-quotes_orders').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        })
    }
}

function DeleteOrders(id = "")
{
    if(confirm('<?=_l('cong_you_must_delete')?>'))
    {
        if($.isNumeric(id))
        {
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['id'] = id;
            $.post(admin_url+'quotes_orders/delete_quotes_orders', data, function(data){
                data = JSON.parse(data);
                if(data.success)
                {
                    $('.table-quotes_orders').DataTable().ajax.reload();
                }
                alert_float(data.alert_type, data.message);
            })
        }
    }
}

function initOrders(id = "")
{
    if($.isNumeric(id))
    {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'quotes_orders/loadViewOrder/'+id, data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.div_modal_orders').html(data.data)
            }
            else
            {
                alert_float(data.alert_type, data.message);
            }
        })
    }
}
</script>
