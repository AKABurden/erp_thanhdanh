
<div class="bold uppercase event_tab_parent">
  <?=_l('info_evaluate')?>
  <a type="button" class="btn wap-show none-event">></a>
</div>
<?php if(!empty($dataMain)) { ?>
  <?php foreach($dataMain as $key => $value) { ?>
    <div class="wap-left-title bold uppercase event_tab event_tab_child" active-tab="<?=$stt?>">
      <?=$value['main']?>
    </div>
  <?php $stt++; ?>
  <?php } ?>
<?php } ?>
<script>
$(".event_tab_parent").click(function(e){
  if($(".event_tab_parent").hasClass('active')) {
    $(".event_tab_parent").removeClass('active');
    $(".event_tab_child").addClass('unactive-child');
    $(".event_tab_child").removeClass('active-child');
  }
  else {
    $(".event_tab_parent").addClass('active');
    $(".event_tab_child").addClass('active-child');
    $(".event_tab_child").removeClass('unactive-child');
  }
});
</script>
<script src="<?=base_url('assets/js/step_by_step.js')?>"></script>