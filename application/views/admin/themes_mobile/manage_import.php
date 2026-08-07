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
              <table id="table-import-mobile" class="dt-table table table-bordered table-hover">
                <thead>
                  <tr>
                    <th class="text-center"><?= _l('#') ?></th>
                    <th class="text-center"><?= _l('ch_code_p') ?></th>
                    <th class="hide"></th> <!-- nhà cung cấp -->
                    <th class="hide"></th> <!-- ngày chứng từ -->
                    <th class="hide"></th> <!-- tổng giá trị -->
                    <th class="hide"></th> <!-- trạng thái -->
                  </tr>
                </thead>
                <tbody>
                  <?php $getAllImport = get_table_where('tblimport',array(),'id DESC'); ?>
                  <?php foreach ($getAllImport as $key => $value) { ?>
                    <?php $getSuppliers = get_table_where('tblsuppliers',array('id'=>$value['suppliers_id']),'','row'); ?>
                    <tr data-key="<?= $key ?>">
                      <td class="text-center details-control"></td>
                      <td>
                        <div class="bold"><?= $value['prefix'].'-'.$value['code'] ?></div>
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
                      <td class="hide sub-tr"><?= (!empty($value['date']) ? _d($value['date']) : '') ?></td>
                      <td class="hide sub-tr"><?= (!empty($value['total']) ? number_format($value['total']) : 0) ?></td>
                      <td class="hide sub-tr-hide">
                        <?php
                          $str = _l('dont_approve');
                          $step = 0;
                          if($value['status'] == 2) {
                            $str = _l('ch_confirm_22');
                            $step = 1;
                          }
                          if($value['warehouseman_id'] > 0) {
                            $str = _l('ch_warehouse_d');
                            $step = 2;
                          }
                        ?>
                        <div class="process_mobile_import">
                          <div class="mright5 is-step-import <?= ($step == 1 ? 'select-step' : (($step > 1) ? 'active' : '')) ?>"></div>
                          <div class="is-step-import <?= ($step == 2 ? 'select-step' : '') ?>"></div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="step_import">
                          <span class="wap-step">(<?= $step.'/2' ?>)</span> 
                          <span class="bold"><?= $str ?></span>
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
    dtItems = $('#table-import-mobile').DataTable({
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

  $('#table-import-mobile tbody').on('click', 'td.details-control', function (e) {
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
    var allTr = $('#table-import-mobile tbody').find('td.details-control');
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
                      <td class="content-sub">Ngày</td>\
                      <td class="content-sub">'+$(vSub).text()+'</td>\
                  </tr>';
        }
        else if(iSub == 2) {
          html += '<tr data-last="'+trKey+'">\
                      <td class="content-sub">Tổng giá trị</td>\
                      <td class="content-sub">'+$(vSub).text()+'</td>\
                  </tr>';
        }
      });
      $(html).insertAfter(tr);
    });
  }
</script>