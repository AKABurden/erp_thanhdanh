<?php init_head(); ?>
<style>
    .bg-sive {
        background: #a7a7a7;
    }
    .bg-sive td {
        padding-top: 1px!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
           
<!--            --><?php //if (is_admin()) { ?>
                <div class="line-sp"></div>
                <a class="btn btn-info mright5 test pull-right H_action_button c_modal" href="<?=admin_url('trouble/modal')?>">
                   <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                   <?php echo _l('create_add_new'); ?>
                </a>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/trouble/modal_excel') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
					<?php echo _l('IMPORT EXCEL'); ?>
                </a>
<!--            --><?php //} ?>
            <div class="clearfix"></div>
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
                            _l('STT'),

                            _l('Mã tiêu chí KPI'),
                            _l('Phòng ban'),
                            _l('c_trouble_code'),
                            _l('c_trouble_name'),
                            _l('Tên tổ - Công đoạn'),
                            _l('Tên công việc'),
                            _l('trouble_violation'),
                            _l('Khung lương 3P'),
                            _l('Nguyên phụ liệu (Material)'),
                            _l('Nhân lực (Man)'),
                            _l('Máy móc (Machine)'),
                            _l('Phương pháp (Method)'),
                            _l('Môi trường (Environment)'),
                            _l('Quy trình xử lý'),
                            _l('Quy trình khắc phục'),
                            _l('options')
                        ),'trouble'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
       var CustomersServerParams = {};
       var tAPI = initDataTable('.table-trouble', admin_url + 'trouble/table', [0], [0], CustomersServerParams, [0, 'desc']);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });

       $('body').on('click', '._delete_row', function() {
           var _href = $(this).attr('href');
           if(confirm('Bạn có chắc chắn muốn xóa?')) {
               $.get(_href, function (result) {
                   result = JSON.parse(result);
                   alert_float(result.alert_type, result.message);
                   if(result.success) {
                       tAPI.ajax.reload();
                   }
                   return false;
               }).fail(function (error) {
                   alert_float('danger', error.responseText);
               });
           }
           return false;
       })
</script>
