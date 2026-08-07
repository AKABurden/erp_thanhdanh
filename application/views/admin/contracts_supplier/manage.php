<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <?php if(has_permission('contracts_supplier','','create')){ ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?php echo admin_url('contracts_supplier/detail'); ?>" class="btn btn-info H_action_button">
                        <?php echo _l('create_add_new'); ?>
                    </a>
                </div>
            <?php } ?>
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
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                    <div class="clearfix mtop20"></div>
                    <?php
                        $table_data = array(
                            _l('#'),
                            _l('Mã Hợp Đồng Mua'),
                            _l('Nhà Cung Cấp'),
                            _l('Tên Hợp Đồng'),
                            _l('Mã Đơn Hàng Mua'),
                            _l('Giá Trị Hợp Đồng'),
                            _l('Ngày Bắt Đầu'),
                            _l('Ngày Kết Thúc'),
                            _l('ch_option'),
                        );
                        render_datatable($table_data, 'contracts_supplier');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var tAPI;
    $(function() {
        var CustomersServerParams = {
          'filterStatus' : '[name="filterStatus"]',
        };
        tAPI = initDataTableCustom('.table-contracts_supplier', admin_url + 'contracts_supplier/table', [0], [0], CustomersServerParams, [1, 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem){
          $(filterItem).on('change', function(){
                tAPI.draw('page');
          })
        })
    })

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
</script>