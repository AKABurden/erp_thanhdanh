<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info test H_action_button">
                   <?php echo _l('Export excel'); ?></a>
            </div>
            <div class="pull-right mright5 H_border">
                <a href="#" class="btn btn-info H_action_button" data-toggle="modal" data-target="#customer_group_modal">
                   <?php echo _l('create_add_new'); ?>
                </a>
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
//                        _l('id'),
                        _l('customer_group_name'),
//                        _l('cong_color'),
//                        _l('Nhóm cha'),
                        _l('options'),
                        ),'customer-groups'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/clients/client_group'); ?>
<?php $this->load->view('admin/clients/modals/type_client'); ?>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-customer-groups', window.location.href, [0], [0], [], ['0', 'desc']);
   });
</script>
</body>
</html>
