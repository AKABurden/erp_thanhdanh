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
	<?php $get_deadline = get_table_where('tblwarranty_export_supplies',array('date_deadline <>'=> NULL, 'date_deadline <='=> date('Y-m-d'))); ?>
	<?php foreach ($get_deadline as $key => $value) { ?>
		<?php
			$result_checkWarehouse = true;
	        $get_item = get_table_where('tblwarranty_supplies',array('id_warranty'=>$value['id_warranty']));
	        foreach ($get_item as $key_item => $value_item) {
	            if($value_item['type_item'] == 'materials') {
	                $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouse');
	                $this->db->where('tblwarehouse_items.id_items', $value_item['id_item']);
	                $this->db->where('tblwarehouse_items.type_items', 'nvl');
	                $quantity_warehouse = $this->db->get('tblwarehouse_items')->row();
	            }
	            else if($value_item['type_item'] == 'supplies') {
	                $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouse');
	                $this->db->where('tblwarehouse_items.id_items', $value_item['id_item']);
	                $this->db->where('tblwarehouse_items.type_items', 'tools');
	                $quantity_warehouse = $this->db->get('tblwarehouse_items')->row();
	            }

	            if($value_item['quantity'] > $quantity_warehouse->quantity_warehouse || $quantity_warehouse->quantity_warehouse == 0) {
	                $result_checkWarehouse = false;
	                break;
	            }
	        }
		?>
		<?php if($result_checkWarehouse == false) { ?>
			<?php $get_warranty = get_table_where('tblwarranty',array('id'=>$value['id_warranty']),'','row'); ?>
			<?php $dem_temp = 125 + ($key * 125); ?>
			<div class="classify" style="top: <?= $dem_temp.'px' ?>" data-top="<?= $dem_temp ?>">
				<div class="classify-img">
					<img width="80" src="https://i.gifer.com/XlQO.gif">
				</div>
				<div class="classify-content">
					<div class="bold uppercase text-center text-danger">Cảnh báo nhập hàng</div>
					<div class="bold uppercase text-center"><a onclick="view_export_supplies(<?= $value['id'] ?>); return false;"><?= $value['name'] ?></a></div>
					<div class="classify-title bold uppercase" style="font-size: 10px;"><a onclick="view_warranty_list(<?= $get_warranty->id ?>); return false;">Phiếu bảo hành: <?= $get_warranty->code ?></a></div>
					<div class="text-center">
						Hạn xuất vật tư: <?= _d($value['date_deadline']) ?>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		<?php } ?>
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
		if(dem_delay > <?= count($get_deadline) ?>) {
			$('.close-classify').addClass('hide');
		}
	}
	$(document).on('click','.close-classify', function (e) {
		$('.wrap-classify-container').css({"bottom": "-140px"});
	});
</script>
<!-- end -->