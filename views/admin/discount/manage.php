<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
    <?php init_head(); ?>
    <style type="text/css">

    .table-discount img{
        height: 20px;
        width: 20px;
    }
    .table-discount thead tr th{
       text-align: center;
    }
    .table-discount tr td:nth-child(2)
    {
                min-width: 95px;
                white-space: unset;
                text-align: center;
    }
    .table-discount tr td:nth-child(3)
    {
                min-width: 105px;
                white-space: unset;

    }
    .table-discount tr td:nth-child(4)
    {
                min-width: 150px;
                white-space: unset;

    }
    .table-discount tr td:nth-child(5)
    {
                min-width: 130px;
                white-space: unset;
                text-align: center;
    }
    .table-discount tr td:nth-child(6)
    {
                min-width: 150px;
                white-space: unset;
                
    }
    .table-discount tr td:nth-child(7)
    {
                min-width: 160px;
                white-space: unset;
    }
    .table-discount tr td:nth-child(8)
    {
                min-width: 120px;
                white-space: unset;
    }
    .table-discount tr td:nth-child(9)
    {
                min-width: 120px;
                white-space: unset;
                text-align: center;
    }
    .table-discount tr td:nth-child(10)
    {
                min-width: 120px;
                white-space: unset;
                text-align: center;
    }
    .no-event_discount {
        pointer-events: none;
        background: #adadad;
    }
    .no-drop_discount {
        cursor: pointer;
    }
    </style>
        <div id="wrapper">
           <div class="panel_s mbot10 H_scroll" id="H_scroll">
              <div class="panel-body ">
                 <div class="_buttons">
                    <span class="bold uppercase fsize18 H_title"><?=$title?></span>
                    <?php if (has_permission('inventory','','create')) { ?>
                    <div class="line-sp"></div>
                    <a href="<?=admin_url('discount/trade')?>"  class="btn btn-info mright5 test pull-right H_action_button">
                   <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                   <?php echo _l('Tạo mới CK thương mại'); ?></a>
                   <div class="line-sp"></div>
                    <a href="<?=admin_url('discount/payment')?>"  class="btn btn-info mright5 test pull-right H_action_button">
                   <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                   <?php echo _l('Tạo mới CK thanh toán'); ?></a>
                    <?php } ?>
                  </div>
                  <div class="clearfix"></div>
              </div>
           </div>
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
                                        <a class="H_filter" class="js-btn-tab" data-id="all">
                                          <?=_l('leads_all')?>(<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li class="">
                                        <a class="H_filter" class="js-btn-tab" data-id="1">
                                          <?=_l('Chiết khấu thương mại')?>(<span class="datail">0</span>)
                                        </a>
                                    </li>
                                    <li class="">
                                        <a class="H_filter" class="js-btn-tab" data-id="2">
                                          <?=_l('Chiết khấu thanh toán')?>(<span class="payment">0</span>)
                                        </a>
                                    </li>
                                  </ul>
                              </div>
                          </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                          <div class="clearfix mtop20"></div>
                          <?php $table_data = array(
                              _l('#'),
                              _l('ch_code_p'),
                              _l('ch_date_p'),
                              _l('ch_name_discount'),
                              _l('discount_type'),
                              _l('market_discount'),
                              _l('date_active'),
                              _l('ch_catestaff_create'),
                              _l('cong_orders'),
                              _l('leads_dt_status'),
                              _l('apply'),
                            );
                            render_datatable($table_data,'discount');
                          ?>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
    <?php init_tail(); ?>
    <div id="view_discount_data"></div>
    <div id="view_discount_payment_data"></div>
    <div id="view_sales_discount_data"></div>
  <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
  <link href="https://datatables.net/download/build/dataTables.responsive.nightly.css" rel="stylesheet" type="text/css" />
  <script src="https://datatables.net/download/build/dataTables.responsive.nightly.js"></script>
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

            var CustomersServerParams = {
              'filterStatus' : '[name="filterStatus"]',
            };
            var tAPI = initDataTable('.table-discount', admin_url+'discount/table', [0], [0], CustomersServerParams,[0, 'desc']);
            $.each(CustomersServerParams, function(filterIndex, filterItem){
              $('' + filterItem).on('change', function(){
                tAPI.ajax.reload();
              });
            });
        });
        $('.table-discount').on('draw.dt', function() {
            get_total_limit();
        });
        function var_status(status,id) {
            {
                dataString={id:id,status:status,[csrfData['token_name']] : csrfData['hash']};
                jQuery.ajax({   
                    type: "post",
                    url:"<?=admin_url()?>discount/update_status",
                    data: dataString,
                    cache: false,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.success == true) {
                            $('.table-discount').DataTable().ajax.reload();
                            alert_float('success', response.message);
                        }
                    }
                });
                return false;
            }
        }
        $(document).on('click', '.delete-remind', function() {
            var r = confirm("<?php echo _l('confirm_action_prompt');?>");
            if (r == false) {
                return false;
            } else {
                $.get($(this).attr('href'), function(response) {
                  alert_float(response.alert_type, response.message);
                    $('.table-discount').DataTable().ajax.reload();
                }, 'json');
            }
            return false;
        });
        function view_discount(id) {
            $('#view_discount_data').html('');
            $.get(admin_url + 'discount/discount_data/'+id).done(function(response) {
            $('#view_discount_data').html(response);
            $('#view_discount').modal({show:true,backdrop:'static'});
            init_selectpicker();
            init_datepicker();
            }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
            }); 
        }
        $('body').on('hidden.bs.modal', '#view_discount', function() {
            $('#view_discount_data').html('');
        });
        function view_discount_payment(id) {
            $('#view_discount_payment_data').html('');
            $.get(admin_url + 'discount/discount_data_payment/'+id).done(function(response) {
            $('#view_discount_payment_data').html(response);
            $('#view_discount_payment').modal({show:true,backdrop:'static'});
            init_selectpicker();
            init_datepicker();
            }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
            }); 
        }
        $('body').on('hidden.bs.modal', '#view_discount_payment', function() {
            $('#view_discount_payment_data').html('');
        });
        function view_sales_discount(id) {
            $('#view_sales_discount_data').html('');
            $.get(admin_url + 'discount/view_sales_discount/'+id).done(function(response) {
            $('#view_sales_discount_data').html(response);
            $('#view_sales_discount').modal({show:true,backdrop:'static'});
            init_selectpicker();
            init_datepicker();
            }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
            }); 
        }
        $('body').on('hidden.bs.modal', '#view_sales_discount', function() {
            $('#view_sales_discount_data').html('');
        });
       function get_total_limit() {
          dataString = {[csrfData['token_name']] : csrfData['hash']};
            jQuery.ajax({
                type: "post",
                url: "<?=admin_url()?>discount/count_all/",
                data: dataString,
                cache: false,
                success: function (data) {
                  data = JSON.parse(data);
                  $('.all').html(data.all);
                  $('.datail').html(data.datail);
                  $('.payment').html(data.payment);            
                  }
            });
        }

        //hoàng crm bổ xung áp dụng vào khách hàng
        $(document).on('click','.apply-discount', function (e) {
            var id = $(this).attr('data-id');
            $('.apply-discount').removeClass('active');
            $('.wap-load').addClass('hide');
            if($(this).find('.wap-load').hasClass('hide')) {
                //xử lý css
                $(this).find('.wap-load').removeClass('hide');
                $(this).addClass('active');
                var dont_active = $('.apply-discount');
                $.each(dont_active,function(i,v){
                    if(!$(this).hasClass('active')) {
                        $(this).parents('td').addClass('no-drop_discount');
                        $(this).addClass('no-event_discount');
                    }
                });

                //xử lý back-end
                var data = {};
                if (typeof(csrfData) !== 'undefined') {
                  data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(admin_url+'discount/apply_discount/'+id, data).done(function(response){
                    response = JSON.parse(response);
                    alert_float(response.alert_type,response.message);
                    $('.apply-discount').removeClass('active');
                    $('.wap-load').addClass('hide');
                    $('.apply-discount').parents('td').removeClass('no-drop_discount');
                    $('.apply-discount').removeClass('no-event_discount');
                });
            }
        });
        //end
    </script>
