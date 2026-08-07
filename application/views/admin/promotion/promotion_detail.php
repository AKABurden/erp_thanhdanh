<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-promotion_detail tr td:nth-child(2){
        min-width: 150px;
        white-space: unset;
    }
    .table-promotion_detail tr td:nth-child(3){
        min-width: 100px;
        white-space: unset;
    }
    .table-promotion_detail tr td:nth-child(4){
        min-width: 150px;
        white-space: unset;
    }
    .table-promotion_detail tr td:nth-child(5){
        min-width: 150px;
        white-space: unset;
    }
    .table-promotion_detail tr td:nth-child(6){
        min-width: 150px;
        white-space: unset;
    }
    .table-promotion_detail tr td:nth-child(7){
        min-width: 200px;
        white-space: unset;
    }
    .staff-profile-image-small-new {
        width: 20px;
        height: 20px;
    }
    .wap-customer {
        background: #ffdfbe;
    }
    .wap-total {
        background: #e4e4e4;
    }
    .wap-show {
        cursor: pointer;
        background: #35b734;
        color: #fff;
        padding: 1px 5px;
        border-radius: 50%;
        border: 1px solid #f7f7f7;
    }
    .wap-hide {
        cursor: pointer;
        background: #ff5757;
        color: #fff;
        padding: 1px 7px;
        border-radius: 50%;
        border: 1px solid #f7f7f7;
    }
</style>
<div id="wrapper">
    <?php if(has_permission('promotion','','create')){ ?>
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?=admin_url('promotion/detail');?>" class="btn btn-info H_action_button">
                    <?php echo _l('create_add_new'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                          <?=_l('leads_all')?> (<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                          <?=_l('tnh_un_approved')?> (<span class="status1">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                          <?=_l('tnh_approved')?> (<span class="status2">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                          <?=_l('in_use')?> (<span class="status3">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                          <?=_l('cong_cancel')?> (<span class="status4">0</span>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <?php render_datatable(array(
                            _l('#'),
                            // _l('promotion_list_name'),
                            _l('promotion_name'),
                            _l('promotion_type'),
                            _l('promotion_method_of_application'),
                            _l('promotion_area_of_application'),
                            _l('promotion_area'),
                            _l('promotion_time'),
                            _l('status'),
                            _l('result'),
                            _l('ch_option'),
                        ),'promotion_detail'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal result -->
<div class="modal_result"></div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    $(function(){
        get_total_limit();
        var CustomersServerParams = {
            'filterStatus' : '[name="filterStatus"]'
        };
        initDataTableCustom('.table-promotion_detail', admin_url+'promotion/table_promotion_detail', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>, fixedColumns = {leftColumns: 2, rightColumns: 0});
        $.each(CustomersServerParams, function(filterIndex, filterItem){
            $(filterItem).on('change', function(){
                $('.table-promotion_detail').DataTable().ajax.reload();
            });
        });
    });
    function delete_promotion(id) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'promotion/delete_promotion/'+id, data).done(function(response){
            response = JSON.parse(response);
            alert_float(response.alert_type,response.message);
            $('.table-promotion_detail').DataTable().ajax.reload();
        });
    }
    function var_status(status,id) {
        dataString={id:id,status:status,[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({   
            type: "post",
            url:"<?=admin_url()?>promotion/update_status",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-promotion_detail').DataTable().ajax.reload();
                    alert_float('success', response.message);
                }
            }
        });
        return false;
    }
    function get_total_limit() {
        dataString = {[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>promotion/count_all/",
            data: dataString,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.status1').html(data.status1);
                $('.status2').html(data.status2);
                $('.status3').html(data.status3);
                $('.status4').html(data.status4);     
            }
        });
    }

    function view_result(id) {
        $('.modal_result').html('');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'promotion/view_result/'+id, data).done(function(response){
            $('.modal_result').append(response);
            $('#view_result_modal').modal({backdrop: 'static', keyboard: false});
        });
    }

    $(document).on('click', '.show_detail', (e)=>{
        var current = $(e.currentTarget);
        if(current.hasClass('show')) {
            current.removeClass('show');
            current.text('Rút gọn');
            current.parents('.parent_show').find('.show_detail_item').removeClass('hide');
        }
        else {
            current.addClass('show');
            current.text('Chi tiết đơn hàng');
            current.parents('.parent_show').find('.show_detail_item').addClass('hide');
        }
    });
</script>
</body>
</html>
