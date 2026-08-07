<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .wap-table {
    display: flex;
    justify-content: center;
  }
  .no-data {
    height: 50px;
    background: #f3f3f3;
  }
</style>
<div id="wrapper" style="height: 100%;">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
         </div>
      </div>
   </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="wap-table mbot30">
                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed; width: 600px;">
                      <tbody>
                        <tr>
                          <td class="text-center" style="width: 20%;">Chu kỳ (Tháng)</td>
                          <td class="text-center">
                            <?php echo render_input('cycle_evaluate','',get_option('cycle_evaluate'),'number'); ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="wap-table">
                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed; width: 600px;">
                      <thead>
                        <tr>
                          <th class="text-center" style="width: 20%;">STT</th>
                          <th class="text-center"><?=_l('month')?></th>
                          <th style="width: 10%;"></th>
                        </tr>
                      </thead>
                      <tbody class="tbody-table">
                        <tr class="trMain">
                          <td></td>
                          <td>
                            <?php echo render_input('month_main','','','text',array('placeholder'=>'31/12')); ?>
                          </td>
                          <td>
                            <a class="btn btn-info add"><i class="fa fa-check"></i></a>
                          </td>
                        </tr>
                        <?php $get_all = get_table_where('tblsetting_date_evaluate'); ?>
                        <?php $number_tr = 0; ?>
                        <?php if($get_all) { ?>
                          <?php foreach ($get_all as $key => $value) { ?>
                            <tr class="trSub content-data">
                              <td class="text-center"><?=++$key?></td>
                              <td class="text-center" data-id="<?=$value['id']?>">
                                <?php echo render_input('month','',$value['month'],'text'); ?>
                              </td>
                              <td class="text-center">
                                <a class="btn btn-danger deleteTritem" data-id="<?=$value['id']?>"><i class="fa fa-remove"></i></a>
                              </td>
                            </tr>
                            <?php $number_tr++; ?>
                          <?php } ?>

                          <?php if($number_tr < 5) { ?>
                            <?php for ($i = $number_tr; $i < 5; $i++) { ?>
                              <tr class="trSub no-data" data-key="<?=$i?>">
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr>
                            <?php } ?>
                          <?php } ?>
                        <?php } else { ?>
                          <?php for ($i=0; $i < 5; $i++) { ?>
                            <tr class="trSub no-data" data-key="<?=$i?>">
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                          <?php } ?>
                        <?php } ?>
                      </tbody>
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
  var number_tr = <?=$number_tr?>;
  $(document).on('click','.add', function (e) {
    var current = $(e.currentTarget);
    var target = current.parents('tr').find('#month_main');

    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['month'] = target.val();
    $.post(admin_url+'setting_date_evaluate/setData', data).done(function(response){
      response = JSON.parse(response);
      //show HTML
      var stt = Number(number_tr) + 1;
      var html = '';
      if(number_tr < 5) {
        $('tr[data-key="'+number_tr+'"]').html('<td class="text-center">'+stt+'</td>\
                                                <td class="text-center" data-id="'+response.id+'">\
                                                  <div class="form-group">\
                                                    <input type="text" id="month" name="month" class="form-control" value="'+target.val()+'">\
                                                  </div>\
                                                </td>\
                                                <td class="text-center">\
                                                  <a class="btn btn-danger deleteTritem" data-id="'+response.id+'"><i class="fa fa-remove"></i></a>\
                                                </td>\
                                              ');
        $('tr[data-key="'+number_tr+'"]').removeClass('no-data');
        $('tr[data-key="'+number_tr+'"]').addClass('content-data');
      }
      else {
        $('.tbody-table').append('<tr class="trSub content-data">\
                                    <td class="text-center">'+stt+'</td>\
                                    <td class="text-center" data-id="'+response.id+'">\
                                      <div class="form-group">\
                                        <input type="text" id="month" name="month" class="form-control" value="'+target.val()+'">\
                                      </div>\
                                    </td>\
                                    <td class="text-center">\
                                      <a class="btn btn-danger deleteTritem" data-id="'+response.id+'"><i class="fa fa-remove"></i></a>\
                                    </td>\
                                </tr>');
      }
      number_tr++;
    });
  });

  $(document).on('click','.deleteTritem', function (e) {
    var current = $(e.currentTarget);
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['id'] = current.attr('data-id');
    $.post(admin_url+'setting_date_evaluate/deleteData', data).done(function(response){
      current.parents('tr').remove();
    });
  });

  $(document).on('change','#month', function (e) {
    var current = $(e.currentTarget);
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['month'] = current.val();
    data['id'] = current.parents('td').attr('data-id');
    $.post(admin_url+'setting_date_evaluate/updateData', data).done(function(response){
    });
  });

  $(document).on('change','#cycle_evaluate', function (e) {
    var current = $(e.currentTarget);
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['cycle_evaluate'] = current.val();
    $.post(admin_url+'setting_date_evaluate/updateCycle_evaluate', data).done(function(response){
    });
  });
</script>