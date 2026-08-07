<!-- popup thông báo đánh giá ncc -->
<style>
	.wrap-classify-container {
		width: 300px;
	    height: 125px;
	    position: fixed;
	    overflow: hidden;
	    bottom: 10px;
	    left: 25px;
	    z-index: 999;
	    transition: all 0.3s;
		-webkit-transition: all 0.3s;
	}
	.classify-content {
		float: left;
	    display: flex;
	    flex-direction: column;
	    align-items: center;
	    justify-content: center;
	    height: 125px;
	    width: calc(100% - 100px);
	}
	.classify {
		height: 125px;
		position: relative;
		background: #fff;
		border: 1px solid #d0d0d0;
		transition: all 0.3s;
		-webkit-transition: all 0.3s;
	}
	.close-classify {
		cursor: pointer;
		position: absolute;
	    top: 1px;
	    right: 1px;
	    font-size: 20px;
	    background: #fff;
	    padding: 0px 5px;
	    color: #f00;
	}
	.classify-img {
		float: left;
		display: flex;
	    flex-direction: column;
	    align-items: center;
	    justify-content: center;
	    height: 125px;
	}
</style>
<div class="wrap-classify-container">
	<?php $get_all_orrder = get_table_where('tblpurchase_order',array('id_tickets_priorities <>'=> NULL,'status <>'=>4,'cancel'=>0)); ?>
	<?php foreach ($get_all_orrder as $key => $value) { ?>
		<?php $dem_temp = 125 + ($key * 125); ?>
		<?php $tickets_priorities = get_table_where('tbltickets_priorities',array('priorityid'=>$value['id_tickets_priorities']),'','row'); ?>
		<?php $suppliers_name = get_table_where('tblsuppliers',array('id'=>$value['suppliers_id']),'','row'); ?>
		<div class="classify" style="top: <?= $dem_temp.'px' ?>" data-top="<?= $dem_temp ?>">
			<div class="classify-img">
				<img width="80" src="https://i.gifer.com/XlQO.gif">
			</div>
			<div class="classify-content">
				<div class="bold uppercase text-center"><?= $suppliers_name->company ?></div>
				<div class="classify-title bold uppercase"><a onclick="view_purchase_order(<?= $value['id'] ?>); return false;">Mã đơn hàng: <?= $value['prefix'].'-'.$value['code'] ?></a></div>
				<div class="text-center">
					<p style="background: <?= $tickets_priorities->color ?>; color: #fff; font-weight: 300; border-radius: 10px; padding: 0px 10px;"><?= $tickets_priorities->name ?></p>
				</div>
				<div class="text-center">
					Ngày giao hàng: <?= _d($value['delivery_date']) ?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php } ?>
	<div class="close-classify hide"><i class="fa fa-times"></i></div>
</div>
<script>
	var dem_delay = 0;
	var dem_temp_delay = 3;
	setInterval(sub_delay, 1000);
	function sub_delay() {
        dem_temp_delay--;
        if(dem_temp_delay == 0) {
            up_classify();
        }
    }
    function up_classify() {
		var allClassify = $('.classify');
		$.each(allClassify, (i,v) => {
			if(dem_delay == 0) {
				var key = i+1;
				var top_classify = $(v).attr('data-top');
				var wap_top = Number(top_classify) - (125 * Number(key));
				$(v).css({"top": wap_top+"px"});
				$(v).attr('data-top', wap_top);
			}
			else {
				var top_classify = $(v).attr('data-top');
				var wap_top = Number(top_classify) - 125;
				$(v).css({"top": wap_top+"px"});
				$(v).attr('data-top', wap_top);
			}
		});
		dem_delay++;
		dem_temp_delay = 4;
		$('.close-classify').removeClass('hide');
		if(dem_delay > <?= count($get_all_orrder) ?>) {
			$('.close-classify').addClass('hide');
		}
	}
	$(document).on('click','.close-classify', function (e) {
		$('.wrap-classify-container').css({"bottom": "-140px"});
	});
</script>
<!-- end -->