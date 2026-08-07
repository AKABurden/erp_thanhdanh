<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if (has_permission('suppliers_group','','create')) { ?>
                <div class="pull-right mright5 H_border">
                    <a href="#" class="btn btn-info test H_action_button" data-toggle="modal" data-target="#suppliers_group_modal">
                        <?php echo _l('create_add_new'); ?></a>
                </div>
            <?php }?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    <?php render_datatable(array(
                        _l('ch_suppliers_group_name'),
                        _l('options'),
                        ),'suppliers-groups'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/suppliers/suppliers_group'); ?>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-suppliers-groups', window.location.href, [1], [1]);
   });
$(document).on('click', '._delete_ch', function() {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            $('.table-suppliers-groups').DataTable().ajax.reload();
        }, 'json');
    }
    return false;
});
</script>
</body>
</html>
