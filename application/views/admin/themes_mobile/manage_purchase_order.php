<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $this->load->view('admin/themes_mobile/style_css')?>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
         </div>
      </div>
   </div>
   <div class="content">
      <div class="row">
          <div class="panel_s">
              <table id="table-purchase-order-mobile" class="dt-table table table-bordered table-hover">
                <thead>
                  <tr>
                    <th class="text-center"><?= _l('#') ?></th>
                    <th class="text-center"><?= _l('ch_code_p') ?></th>
                    <th class="hide"></th> <!-- nhà cung cấp -->
                    <th class="hide"></th> <!-- Tổng giá trị -->
                    <th class="hide"></th> <!-- Tổng chi -->
                    <th class="hide"></th> <!-- quy trình -->
                  </tr>
                </thead>
                <tbody>
                  <?php $getAllPurchases = get_table_where('tblpurchase_order',array(),'id DESC'); ?>
                  <?php foreach ($getAllPurchases as $key => $value) { ?>
                    <?php $getSuppliers = get_table_where('tblsuppliers',array('id'=>$value['suppliers_id']),'','row'); ?>
                    <tr data-key="<?= $key ?>">
                      <td class="text-center details-control"></td>
                      <td>
                        <!-- search -->
                        <!-- <div class="hide">all</div> -->
                        <!-- end -->
                        <div class="bold"><?= $value['prefix'].$value['code'] ?></div>
                        <div>
                          <?php
                            echo staff_profile_image($value['staff_create'], array('image-small-mobile mright5'), 'small', array(
                                'data-toggle' => 'tooltip',
                                'data-title' => get_staff_full_name($value['staff_create'])
                            )).get_staff_full_name($value['staff_create']);
                          ?>
                        </div>
                      </td>
                      <td class="hide sub-tr"><?= ($getSuppliers && $getSuppliers->company ? $getSuppliers->company : '') ?></td>
                      <td class="hide sub-tr"><?= number_format($value['totalAll_suppliers']) ?></td>
                      <td class="hide sub-tr">
                        <?php
                          $total = $value['price_other_expenses'] + $value['amount_paid'];
                          echo number_format($total);
                        ?>
                      </td>
                      <td class="hide sub-tr-hide">
                        <?php
                          $str = '';
                          $step = 0;
                          $step_cancel = 0;
                          if($value['status'] == 1) {
                            $str = _l('create');
                            $step = 1;
                          }
                          else if($value['status'] == 2) {
                            $str = _l('proceed');
                            $step = 2;
                          }
                          else if($value['status'] == 3) {
                            $str = _l('accept');
                            $step = 3;
                          }
                          if($value['status'] == 3) {
                            $import = get_table_where('tblimport',array('id_order'=>$value['id']),'','row');
                            if($import) {
                              $str = _l('add_items');
                              $step = 4;
                            }
                          }
                          if($value['cancel'] != 0) {
                            $step_cancel = 1;
                          }
                        ?>
                        <div class="process_mobile_purchase_order">
                          <div class="mright5 is-step <?= ($step == 1 ? 'select-step' : (($step > 1) ? 'active' : '')) ?>"></div>
                          <div class="mright5 is-step <?= ($step == 2 ? 'select-step' : (($step > 2) ? 'active' : '')) ?>"></div>
                          <div class="mright5 is-step <?= ($step == 3 ? 'select-step' : (($step > 3) ? 'active' : '')) ?>"></div>
                          <div class="mright5 is-step <?= ($step == 4 ? 'select-step' : '') ?>"></div>
                          <div class="is-step <?= ($step_cancel == 1 ? 'cancel-step' : '') ?>"></div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="step_purchase_order">
                          <span class="count_step wap-step">(<?= ($step_cancel == 1 ? '5/5' : $step.'/5') ?>)</span> 
                          <span class="bold"><?= ($step_cancel == 1 ? _l('ch_cancel') : $str) ?></span>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
          </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
  var dtItems = '';
  $(document).ready(function() {
    dtItems = $('#table-purchase-order-mobile').DataTable({
      "bLengthChange" : true,
      "language": app.lang.datatables,
      "pageLength": app.options.tables_pagination_limit,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "destroy": true,
      dom: "<'row'><'row'<'col-md-7'l><'clearfix'><'ln'><'col-md-5'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>",
      "initComplete": function(settings, json) {
        var t = this;
        t.parents('.table-loading').removeClass('table-loading');
        t.removeClass('dt-table-loading');
      },
      "footerCallback": function ( row, data, start, end, display ) {
      }
    });
    $("div.ln").html('<br><br>');

    reloadTable();
    dtItems.on( 'draw.dt', function () {
      reloadTable();
      $('tr').removeClass('shown');
    });
  });

  $('#table-purchase-order-mobile tbody').on('click', 'td.details-control', function (e) {
      var target = $(e.currentTarget);
      var tr = target.parents('tr');
      var trKey = target.parents('tr').attr('data-key');

      if(tr.hasClass('shown')) {
        tr.removeClass('shown');
        var allTrHide = target.parents('tbody').find('.tr-hide[data-hide="'+trKey+'"]');
        $.each(allTrHide, function(iTrHide, vTrHide){
          $(vTrHide).remove();
        });
      }
      else {
        var allSub_tr = tr.find('.sub-tr-hide');
        var html = '';
        $.each(allSub_tr, function(iSub, vSub){
          html += '<tr class="tr-hide" data-hide="'+trKey+'">\
                      <td colspan="2">'+$(vSub).html()+'</td>\
                  </tr>';
        });
        $(html).insertAfter($('tr[data-last="'+trKey+'"]'));
        tr.addClass('shown');
      }
  });

  function reloadTable() {
    var allTr = $('#table-purchase-order-mobile tbody').find('td.details-control');
    $.each(allTr, function(i, v){
      var tr = $(v).parents('tr');
      var trKey = $(v).parents('tr').attr('data-key');
      var allSub_tr = tr.find('.sub-tr');
      var html = '';
      $.each(allSub_tr, function(iSub, vSub){
        if(iSub == 0) {
          html += '<tr>\
                      <td class="content-sub">Nhà cung cấp</td>\
                      <td class="content-sub">'+$(vSub).text()+'</td>\
                  </tr>';
        }
        else if(iSub == 1) {
          html += '<tr>\
                      <td class="content-sub">Tổng giá trị</td>\
                      <td class="content-sub">'+$(vSub).text()+'</td>\
                  </tr>';
        }
        else if(iSub == 2) {
          html += '<tr data-last="'+trKey+'">\
                      <td class="content-sub">Tổng chi</td>\
                      <td class="content-sub">'+$(vSub).text()+'</td>\
                  </tr>';
        }
      });
      $(html).insertAfter(tr);
    });
  }

  // $('.H_filter').click(function(e) {
  //   var target = $(e.currentTarget);
  //   var value = target.attr('data-id');
  //   if(value == 'all') {
  //     dtItems.search('all').draw();
  //   }
  // });
</script>