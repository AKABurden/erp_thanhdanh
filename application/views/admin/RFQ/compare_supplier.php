<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .table-color {
    table-layout: fixed;
  }
  .table-color tbody tr td {
    border: 1px solid #a2a2a294;
  }
  .width200 {
    width: 200px;
  }
  .width100 {
    width: 100px;
  }
  .table-color td, .table-color th {
    white-space: unset;
  }
  .table-color td{
    background: #fff;
  }
  .table-color th{
    background: #3f9ad6;
  }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <hr class="hr-panel-heading mtop0"/>
            <div class="panel_s">
              <div class="panel-body no-padding">
                <div class="col-md-6 pull-left">
                  <div class="panel panel-success">
                    <?php 
                      $type = '';
                      if (!isset($dataMain))
                        $type = 'warning';
                      elseif ($dataMain->status == 1)
                        $type = 'warning';
                      elseif ($dataMain->status == 2)
                        $type = 'success';
                    ?>
                    <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                      <?php 
                        if (isset($dataMain))
                          {
                          $status = format_supplers_status($dataMain->status, '', false);
                        }
                        else
                          {
                          $status = format_supplers_status(-1, '', false);
                        }
                      ?>
                      <span><?= $status ?></span>
                    </div>
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=_l('lead_general_info')?></h3>
                    </div>
                    <div class="panel-body">
                      <div class="well well-sm">
                        <div class="row">
                          <div class="col-md-6">
                            <div>
                              <b><?=_l('ch_code_p')?>: </b><?php echo $dataMain->prefix.'-'.$dataMain->code ?>
                            </div>
                            <div>
                              <b><?=_l('ch_staff_crate_rfq')?>: </b>
                              <?php echo staff_profile_image($dataMain->staff_create, array('staff-profile-image-small mright5'), 'small', array(
                                        'data-toggle' => 'tooltip',
                                        'data-title' => get_staff_full_name($dataMain->staff_create)
                                    )).get_staff_full_name($dataMain->staff_create)?>
                            </div>
                            <div>
                              <b><?=_l('ch_date_p')?>: </b>
                              <?php echo _d($dataMain->date_create)?>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <?php
                              $history_status = explode('|',$dataMain->history_status);
                              foreach ($history_status as $key => $value) {
                                $data=explode(',',$value);
                                if(is_numeric($data[0])) { ?>
                                  <div><b><?=_l('ch_status_import')?>: <?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                                        'data-toggle' => 'tooltip',
                                        'data-title' => ' Vào lúc: '._dt($data[1])
                                    )).get_staff_full_name($data[0])?>
                                  </div>
                            <?php } } ?>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 center bold uppercase fsize18"><?=_l('compare_supplier_by_number')?> <?php echo $dataMain->prefix.'-'.$dataMain->code ?></div>
                <div class="clearfix"></div>
                <?php if(isset($bodyMain)) { ?>
                  <div class="table-responsive">
                    <table class="table table-color no-mtop">
                      <thead id="thead-scroll">
                        <tr>
                          <th class="width200 scroll-table uppercase center"><?=_l('supplier')?></th>
                          <?php $numberChild = 0; ?>
                          <?php foreach ($bodyMain as $key => $value) { ?>
                            <?php $dem_temp = get_table_where('tblevaluation_criteria_children',array('id_evaluation'=>$value['idParent'])); ?>
                            <th colspan="<?=count($dem_temp)?>" class="center"><?=$value['parent']?></th>
                          <?php } ?>
                        </tr>
                        <tr>
                          <th class="width200 scroll-table" style="border-top: 0 !important;"></th>
                          <?php foreach ($bodyMain as $key => $value) { ?>
                            <?php $getChild = get_table_where('tblevaluation_criteria_children',array('id_evaluation'=>$value['idParent']),'id ASC'); ?>
                              <?php foreach ($getChild as $keyChild => $valueChild) { ?>
                                <th class="center width100"><?=$valueChild['name_children']?></th>
                                <?php $numberChild++; ?>
                              <?php } ?>
                          <?php } ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($arrSupplier as $key => $value) { ?>
                        <?php $numberChild_body = 0; ?>
                          <tr>
                            <td class="width200 scroll-table"><?=$value['company']?></td>
                            <?php foreach ($bodyMain as $keyMain => $valueMain) { ?>
                              <?php $getChild = get_table_where('tblevaluation_criteria_children',array('id_evaluation'=>$valueMain['idParent']),'id ASC'); ?>
                              <?php foreach ($valueMain['child'] as $keyChildMain => $valueChildMain) { ?>
                                <?php foreach ($getChild as $keyChild => $valueChild) { ?>
                                  <?php if ($valueChildMain['suppliers_id'] == $value['id'] && $valueChildMain['id_evaluation_criteria_children'] == $valueChild['id']) { ?>
                                    <td class="center width100"><?=$valueChildMain['point']?></td>
                                    <?php $numberChild_body++; ?>
                                    <?php break; ?>
                                  <?php } ?>
                                <?php } ?>
                              <?php } ?>
                            <?php } ?>
                            <?php if($numberChild_body < $numberChild) { ?>
                              <?php for ($i = $numberChild_body; $i < $numberChild; $i++) { ?>
                                <td class="center width100">0</td>
                              <?php } ?>
                            <?php } ?>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } else { ?>
                  <div class="col-md-12 center">
                    <div class="panel panel-danger">
                      <div class="panel-body"><?=_l('no_data')?></div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
$( document ).ready(function() {
  reWidth();
});
function reWidth() {
  var width = <?=$numberChild?>; //số lượng cột trong table
  width = width*100; //width các cột
  width = width+200; //width cột đầu tiên
  document.getElementsByClassName("table-color")[0].style.width = width+'px';
}
// var lastScrollLeft = 0;
// $('div.table-responsive').scroll(function() {
//     var documentScrollLeft = $('div.table-responsive').scrollLeft();
//     if (lastScrollLeft != documentScrollLeft) {
//       var scroll = document.getElementsByClassName("scroll-table");
//       for (i = 0; i < scroll.length; i++) {
//           var width_scroll = scroll[i].width();
//           console.log(width_scroll);
//           scroll[i].style.position = "absolute";
//       }
//     }
//     else {
//       var scroll = document.getElementsByClassName("scroll-table");
//       for (i = 0; i < scroll.length; i++) {
//           scroll[i].style.position = "unset";
//       }
//     }
// });
</script>
