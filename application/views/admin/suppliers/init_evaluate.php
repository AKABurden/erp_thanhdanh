
<?php if(!empty($dataMain)) { ?>
    <?php foreach($dataMain as $key => $value) { ?>
      <div class="fieldset" role-fieldset="<?=$stt?>">
        <div class="col-md-12">
            <?php foreach ($value['sub'] as $keySub => $valueSub) { ?>
            	<div class="wap-content <?=$keySub % 2 == 0 ? 'firt' : 'second'?> <?=$keySub == 0 ? 'mtop10' : ''?>">
                    <span class="text-muted lead-field-heading no-mtop bold"><?=$valueSub['name']?>: </span>
                    <?php $point = get_table_where('tblevaluate_suppliers',array('suppliers_id' => $id, 'id_evaluation_criteria' => $value['id_main'],'id_evaluation_criteria_children' => $valueSub['id_sub']),'tblevaluate_suppliers.id desc','row'); ?>
                    <?php $point = (!empty($point) ? $point->point : NULL);?>
                    <span class="bold font-medium-xs"><?=!empty($point) ? $point : '-'?></span>
                	</div>
            <?php } ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php $stt++; ?>
  <?php } ?>
<?php } ?>
<script>
$(document).ready(function() {
    setTimeout(function(){ reSizeHeight(); }, 100);
});
function reSizeHeight() {
	var template_count = $('.wap-left');

	$.each(template_count, function(i,v) {
		if(i == 1) {
			var Height = $($(this)).height();
      Height = Number(Height) - 3;
			$($(this).parent().find('.wap-right').css("height", Height+"px"));
		}
	});
}
</script>
