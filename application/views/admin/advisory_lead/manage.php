<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .progressbar {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }
    .progressbar li {
        list-style-type: none;
        width: 16%;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        /*text-transform: uppercase;*/
        color: #7d7d7d;
        z-index: 0;
    }
    .progressbar li:before {
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
    .progressbar li:after {
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
    .progressbar li.active {
        color: green;
    }
    .progressbar li.active:before {
        border-color: #55b776;
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
    .table-advisory_lead tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 100px;
    }
    .table-advisory_lead tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 100px;
    }
    .table-advisory_lead tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 180px;
    }
    .table-advisory_lead tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 150px;
    }
    .table-advisory_lead tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 80px;
    }
    .table-advisory_lead tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 80px;
    }
    .table-advisory_lead tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 600px;
    }
</style>
<!---->
<!--<style>-->
<!--    @media only screen and (max-width: 800px) {-->
<!--        .progressbar {-->
<!--            position: relative;-->
<!--            padding-left: 45px;-->
<!--            list-style: none;-->
<!--        }-->
<!---->
<!--        .progressbar::before {-->
<!--            display: inline-block;-->
<!--            content: '';-->
<!--            position: absolute;-->
<!--            top: 0;-->
<!--            left: 15px;-->
<!--            width: 10px;-->
<!--            height: 100%;-->
<!--            border-left: 2px solid #CCC;-->
<!--        }-->
<!---->
<!--        .progressbar li {-->
<!--            position: relative;-->
<!--            counter-increment: list;-->
<!--        }-->
<!---->
<!--        .progressbar li:not(:last-child) {-->
<!--            padding-bottom: 20px;-->
<!--        }-->
<!---->
<!--        .progressbar li::before {-->
<!--            display: inline-block;-->
<!--            content: '';-->
<!--            position: absolute;-->
<!--            left: -30px;-->
<!--            height: 100%;-->
<!--            width: 10px;-->
<!--        }-->
<!---->
<!--        .progressbar li::after {-->
<!--            content: '';-->
<!--            display: inline-block;-->
<!--            position: absolute;-->
<!--            top: 0;-->
<!--            left: -37px;-->
<!--            width: 12px;-->
<!--            height: 12px;-->
<!--            border: 2px solid #CCC;-->
<!--            border-radius: 50%;-->
<!--            background-color: #FFF;-->
<!--        }-->
<!---->
<!--    }-->
<!--</style>-->

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a class="btn btn-info mright5 test pull-right H_action_button">
               <?php echo _l('Export excel'); ?></a>
            <a class="btn btn-info mright5 test pull-right H_action_button" data-toggle="collapse" data-target="#search">
                <?php echo _l('search'); ?>
            </a>
            <div class="line-sp"></div>
            <a href="#" class="btn btn-info pull-right H_action_button" onclick="editAdvisory_lead()">
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
                        <div id="search" class="collapse">
                            <div class="col-md-3">
                                <?php echo render_input('name_lead', 'cong_name_lead');?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('code_advisory', 'cong_code_advisory');?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('code_lead', 'cong_code_lead');?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('vip_code', 'vip_code');?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-3">
                                <?php
                                    $data_vip_rating = [
                                            ['id' => '1', 'name' => _l('cong_1_start')],
                                            ['id' => '2', 'name' => _l('cong_2_start')],
                                            ['id' => '3', 'name' => _l('cong_3_start')],
                                            ['id' => '4', 'name' => _l('cong_4_start')],
                                            ['id' => '5', 'name' => _l('cong_5_start')]
                                    ];
                                    echo render_select('vip_rating_lead', $data_vip_rating, ['id', 'name'], 'cong_vip_rating');
                                ?>
                            </div>
                            <div class="clearfix"></div>

                            <hr class="hr-panel-heading" />
                        </div>
                        <div  class="btn-group mbot15">
                            <button type="button" data-toggle="tab" class="btn font11 btn-filter filter_all btn-icon btn-info active"><?=_l('cong_all')?></button>
                            <?php if(!empty($client_detail)){?>
                                <?php foreach($client_detail as $key => $value){
                                    $tbody_search = "<div class='div-form-group' id_data='".$value['id']."'>";
                                    $tbody_search .= "    <div class='form-group'>
                                                                <label  class='control-label'>"._l('cong_date_start')."</label>
                                                                <div class='input-group date'>
                                                                    <input type='text' class='form-control datepicker date_start_filter' id_data='".$value['id']."' value='' autocomplete='off' aria-invalid='false'>
                                                                    <div class='input-group-addon'><i class='fa fa-calendar calendar-icon'></i></div>
                                                                </div>
                                                          </div>";
                                    $tbody_search .= "    <div class='form-group'>
                                                                <label class='control-label'>"._l('cong_date_end')."</label>
                                                                <div class='input-group date'>
                                                                    <input type='text'  class='form-control datepicker date_end_filter' id_data='".$value['id']."' value='' autocomplete='off' aria-invalid='false'>
                                                                    <div class='input-group-addon'><i class='fa fa-calendar calendar-icon'></i></div>
                                                                </div>
                                                          </div>";
                                    $tbody_search .= "    <button type='button' class='btn btn-success search-btn'>"._l('cong_filter_data')."</button>";
                                    $tbody_search .= "</div>"
                                    ?>
                                        <button type="button" data-toggle="popover" id_data="<?=$value['id']?>" data-placement="bottom" data-html="true" data-content="<?=$tbody_search?>" class="btn-filter btn font11 btn-icon btn-info">
                                            <?=$value['name']?>
                                        </button>
                                <?php } ?>
                            <?php } ?>
                            <?php echo form_hidden('date_start'); ?>
                            <?php echo form_hidden('date_end'); ?>
                            <?php echo form_hidden('procedure'); ?>
                        </div>
                        <div class="clearfix"></div>
                            <?php render_datatable(array(
                                _l('cong_fullcode_advisory'),
                                _l('cong_lead'),
                                _l('cong_date_start'),
                                _l('cong_product_other_buy'),
                                _l('cong_address_other_buy'),
                                _l('cong_date_create'),
                                _l('cong_create_by'),
                                _l('cong_step_advisory'),
                            ),'advisory_lead'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_advisory_lead" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<?php init_tail(); ?>
<script>

    var filterList = {
        'datestart' : '[name="date_start"]',
        'dateend' : '[name="date_end"]',
        'procedure' : '[name="procedure"]',
        'name_lead' : '[name="name_lead"]',
        'code_advisory' : '[name="code_advisory"]',
        'code_lead' : '[name="code_lead"]',
        'vip_code' : '[name="vip_code"]',
        'vip_rating_lead' : '[name="vip_rating_lead"]'
    };
    $(function(){
        initDataTable('.table-advisory_lead', admin_url+'advisory_lead/table', [0], [0], filterList, [5, 'desc']);
    });

    $.each(filterList, function(i, filter){
        $(filter).on('change', function(e){
            if($('.table-advisory_lead').hasClass('dataTable'))
            {
                $('.table-advisory_lead').DataTable().ajax.reload();
            }
        })
    })

    function editAdvisory_lead(id = "", _this)
    {
        var button = $(_this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        if($.isNumeric(id))
        {
            data['id'] = id;
        }
        $.post(admin_url+'advisory_lead/getModal', data, function(data){
            $('#modal_advisory_lead').html(data);
            $('#modal_advisory_lead').modal('show');
        }).always(function() {
            button.button('reset')
        });
    }

    function deleteAdvisory_lead(id = "", _this) {
        if($.isNumeric(id))
        {
            if(confirm("<?=_l('cong_you_must_delete')?>"))
            {
                var button = $(_this);
                button.button({loadingText: '<?=_l('cong_please_wait')?>'});
                button.button('loading');
                var data = {};
                if (typeof (csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['id'] = id;
                $.post(admin_url+'advisory_lead/delete_advisory_lead', data, function(data){
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    if(data.success)
                    {
                        $('.table-advisory_lead').DataTable().ajax.reload();
                    }
                }).always(function() {
                    button.button('reset')
                });
            }
        }
    }
    $('body').on('click', '.update_status_lead', function(e){
        var id_assigned  = $(this).attr('id-data');
        var status_procedure  = $(this).attr('status-procedure');
        var data = {};
        var button = $(this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id_assigned;
        data['status_procedure'] = status_procedure;
        $.post(admin_url+'advisory_lead/update_status/', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset')
        });
    })


    $('.table-advisory_lead').on('draw.dt', function() {
        var invoiceReportsTable = $(this).DataTable();
        var _table = $('.table-advisory_lead');
        var lengthTh = _table.find('thead tr th').length;

        var TD_child = _table.find('tbody').find('tr.TD_child');
        TD_child.find('td:nth-child(1)').attr('colspan', lengthTh);
        TD_child.find('td:gt(0)').remove();
    })

    function restore_advisory_lead(id = "", _this)
    {
        var button = $(_this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        $.post(admin_url+'advisory_lead/restore_advisory_lead', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset')
        });
    }
    function BreakAdvisory(id = "", status, _this)
    {
        var button = $(_this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        $.post(admin_url+'advisory_lead/break_advisory', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset')
        });
    }

</script>
<script>
    $('body').on('click', 'button.search-btn', function(e){
        var _div_parent = $(this).parent('.div-form-group');
        var id_procedure = _div_parent.attr('id_data');
        var  date_start = _div_parent.find('.date_start_filter').val();
        var  date_end = _div_parent.find('.date_end_filter').val();
        $('input[name="date_start"]').val(date_start);
        $('input[name="date_end"]').val(date_end);
        $('input[name="procedure"]').val(id_procedure);
        $('.table-advisory_lead').DataTable().ajax.reload();
    })
    $(document).on('click', '.btn-filter',function(e){
        if(!$(this).hasClass('filter_all'))
        {
            var id_procedure = $(this).attr('id_data');
            var procedure = $('input[name="procedure"]').val();
            if(id_procedure == procedure)
            {
                var  date_start = $('input[name="date_start"]').val();
                var  date_end = $('input[name="date_end"]').val();
                var _div_parent = $(this).parent('.div-form-group');
                $('.date_start_filter[id_data="'+procedure+'"]').val(date_start);
                $('.date_end_filter[id_data="'+procedure+'"]').val(date_end);
            }
            init_datepicker();
        }
        else
        {
            $('input[name="procedure"]').val('');
            $('input[name="date_start"]').val('');
            $('input[name="date_end"]').val('');
            $('.table-advisory_lead').DataTable().ajax.reload();
        }
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
    })
</script>


</body>
</html>
