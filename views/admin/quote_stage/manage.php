<?php init_head(); ?>
<style>
    .bg-sive {
        background: #a7a7a7;
    }
    .bg-sive td {
        padding-top: 1px!important;
    }
    .table-quote_stage tr td {
        white-space: nowrap;
    }
    .width120 {
        width: 120px;
        text-align: right!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <div class="line-sp"></div>
             <a href="<?= base_url('admin/quote_stage/detail') ?>" class="btn btn-info pull-right mright10 H_action_button">
                 <i class="fa fa-plus" style="display: initial;" aria-hidden="true"></i>
                <?php echo _l('Tạo mới'); ?>
            </a>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?= render_select('quote_stage_search', (!empty($quote_stage) ? $quote_stage : []), ['id', 'code', 'name'], 'Bảng giá công đoạn')?>
                            </div>
                            <div class="col-md-3">
								<?= render_select('client_search', (!empty($data_client) ? $data_client : []), ['userid', 'company_short', 'zcode'], 'Khách hàng')?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="div_table">
                            <table class=" table table-quote_stage dont-responsive-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mã bảng giá</th>
                                        <th>Tên bảng giá</th>
                                        <th>Khách hàng</th>
                                        <th>Thuộc tính</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
       var CustomersServerParams = {
           'quote_stage_search' : '#quote_stage_search',
           'client_search' : '#client_search',
       };
       var tAPI = initDataTable('.table-quote_stage', admin_url + 'quote_stage/table', [0], [0], CustomersServerParams, [0, 'desc']);
       $('select[name="quote_stage_search"], select[name="client_search"]').on('change',function(){
           tAPI.ajax.reload();
       });

       $('body').on('click', '.deleteQuoteStage', function() {

           if(!confirm('Bạn có chắc muốn xóa?')) {
               return false;
           }
           var id = $(this).data('id');
           var data = {};
           if (typeof(csrfData) !== 'undefined') {
               data[csrfData['token_name']] = csrfData['hash'];
           }
           $.post(admin_url + 'quote_stage/delete/' + id, data, function (result) {
               result = JSON.parse(result);
               if(result.success) {
                   tAPI.ajax.reload();
               }
               alert_float(result.alert_type, result.message);
           }).fail(function (err) {
               alert_float('danger', err.responseText);
           });
       })
</script>
