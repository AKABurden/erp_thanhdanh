<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" id="jquery-gantt-css" href="<?= base_url('assets/plugins/gantt/css/style.css?v=2.3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('gantt.css') ?>">
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="<?= base_url('admin/orders') ?>" class="btn btn-primary pull-right H_action_button">
                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                <?php echo _l('tnh_orders'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="row mbot10">
                            <div class="col-md-12">
                                <div class="pull-right">
                                    <table class="tnh-table table-hover table-bordered fn-gantt" cellpadding="3">
                                        <tbody>
                                            <tr>
                                                <td class="ganttPrimary" style="width: 30px;"></td>
                                                <td><?= lang('tnh_bt') ?></td>
                                                <td class="ganttGray" style="width: 30px;"></td>
                                                <td><?= lang('tnh_ht') ?></td>
                                                <td class="ganttRed" style="width: 30px;"></td>
                                                <td><?= lang('tnh_trh') ?></td>
                                                <td class="ganttGreen" style="width: 30px;"></td>
                                                <td><?= lang('tnh_th') ?></td>
                                                <td class="ganttYellow" style="width: 30px;"></td>
                                                <td><?= lang('tnh_sth') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row mbot10">
                            <div class="col-md-4">
                                <?= lang('tnh_reference_orders') ?>
                                <input type="text" name="order_id" id="orders" data-placeholder="<?= lang('tnh_reference_orders') ?>" style="width: 100%;" value="<?= $this->input->post('order_id') ?>">
                            </div>
                            <div class="col-md-4 mtop20">
                                <button type="submit" name="search" value="search" class="btn btn-info"><?= lang('search') ?></button>
                                <button type="submit" name="search" value="unsearch" class="btn btn-danger"><?= lang('tnh_un_search') ?></button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="gantt"></div>
                        <div class="text-center">
                            <ul class="pagination">
                                <?php
                                    $page = 1;
                                    for ($i = 0; $i < $numPages; $i++) {
                                        $active = $page == $pageCurrent ? 'active' : '';
                                        echo '<li class="'.$active.'"><a href="'.base_url('admin/orders/gannt_orders?page='.$page).'">'.$page.'</a></li>';
                                        $page++;
                                    }
                                ?>
                          </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <?php $this->load->view('admin/activity_log/timeline_log', ['module' => 'quotes|orders|returned_goods|business_plan'])  ?>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" id="jquery-gantt-js" src="<?= base_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js?v=2.3.3') ?>"></script>
<script type="text/javascript">
    // var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {status_table: '#status_table'};
    var oTable = '';
</script>
<script type="text/javascript">
    var gantt_data = {};
</script>
<script>
    $(function(){
        ajaxSelectParamsCallback('#orders', 'admin/orders/searchOrders', $('#orders').val(), false);
        <?php if (!empty($gantt_data)): ?>
            gantt_data = <?= json_encode($gantt_data) ?>;
        <?php endif ?>
        $(function(){
            $("#gantt").gantt({
                source: gantt_data,
                itemsPerPage: 10000000000000,
                months: app.months_json,
                navigate: 'scroll',
                onRender: function(ev) {
                    var descLabelLength = $('.leftPanel .desc .fn-label').length;
                    for (var i = 0; i < descLabelLength; i++) {
                        el = $('.leftPanel .desc .fn-label')[i];
                        descLabel = $(el).html();
                        if (descLabel == "orders") {
                            $(el).html('');
                        }
                    }
                    $('#gantt .leftPanel .name .fn-label:empty').parents('.name').css({
                        'background': 'initial',
                        'width': '25px',
                    });
                    var emptyLength = $('#gantt .leftPanel .name .fn-label:empty').length;
                    for (var i = 0; i < emptyLength; i++) {
                        el = $('#gantt .leftPanel .name .fn-label:empty')[i];
                        descNext = $(el).closest('div').next('.desc');
                        descNext.css({
                            'width': '275px',
                        });
                    }
                },
                onItemClick: function(data) {
                    if(typeof(data.order_id) != 'undefined') {
                        $.ajax({
                            url: site.base_url+'admin/orders/view_order/'+data.order_id,
                            type: 'GET',
                            dataType: 'html',
                            data: {
                                token: hash
                            },
                        })
                        .done(function(data) {
                            $('#tnhModal').html(data);
                        })
                        .fail(function() {
                            console.log("error");
                        });
                        $('#tnhModal').modal({backdrop: 'static', keyboard: true});
                    }
                },
            });
        });
    });
</script>
