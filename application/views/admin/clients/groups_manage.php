<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-customer-groups tr td:nth-child(1) {
        width: 80px;
        white-space: unset;
        text-align: center;
    }
    .table-customer-groups tr td:nth-child(4) {
        width: 120px;
        white-space: unset;
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
                <a onclick="export_excel();"  class="btn btn-info mright5 hide test pull-right H_action_button"><?php echo _l('c_export_excel'); ?></a>
            </div>
            <a href="<?=admin_url('clients/modal_excel_import')?>" class="btn btn-info hide mright5 test pull-right H_action_button c_modal"><?php echo _l('c_import_excel'); ?></a>
            <div class="pull-right mright5 H_border">
                <a href="#" class="btn btn-info H_action_button" onclick="edit()">
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
                        _l('Số Thứ Tự'),
                        _l('Mã Nhóm'),
                        _l('Tên Nhóm'),
                        _l('Phân Loại'),
                        _l('Nhóm Chứng Nhận'),
                        _l('Tiêu Chuẩn Áp Dụng'),
                        _l('ĐV Kiểm-Chứng Nhận'),
                        _l('Ngày Bắt Đầu'),
                        _l('Ngày Tái Tục'),
                        _l('Tiêu Chuẩn Sản Phẩm'),
                        _l('% Chiết Khấu'),
                        _l('Tác vụ'),
                        ),'customer-groups'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="groups_manage_data"></div>
<?php $this->load->view('admin/clients/client_group'); ?>
<?php $this->load->view('admin/clients/modals/type_client'); ?>
<?php init_tail(); ?>
<script>
    var tAPI = '';
   $(function(){
       tAPI = initDataTable('.table-customer-groups', window.location.href, [0], [0], [], ['0', 'desc']);
       tAPI.columns(3).visible(false, false);
       tAPI.columns(4).visible(false, false);
       tAPI.columns(5).visible(false, false);
       tAPI.columns(6).visible(false, false);
       tAPI.columns(7).visible(false, false);
       tAPI.columns(8).visible(false, false);
       tAPI.columns(9).visible(false, false);
       tAPI.columns(10).visible(false, false);
   });

   function edit(id = 0){
       $('#groups_manage_data').html('');
       $.get(admin_url + 'clients/edit_group/' + id).done(function(response) {
           $('#groups_manage_data').html(response);
           $('#customer_group_modal').modal('show');
       }).fail(function(error) {
           var response = JSON.parse(error.responseText);
           alert_float('danger', response.message);
       });
   }
   function export_excel() {
       var get = "?data=true";
       window.open(admin_url + 'clients/export_excel_group' + get, '_blank');
   }
</script>
</body>
</html>
