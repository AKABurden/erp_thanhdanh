<div class="row">
	<div class="col-md-12">
		<div>Chi phí nguyên vật liệu trực tiếp: <b><?= formatMoney($directMaterial) ?></b></div>
		<div>Chi phí nhân công trực tiếp: <b><a class="tnh-modal-attr" start_date="<?= $start_date ?>" end_date="<?= $end_date ?>" type_object="chi_phi_nhan_cong_truc_tiep" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/costing/infoDetailCosting') ?>"><?= formatMoney($directLaborCosting) ?></a></b></div>
		<div>Chi phí sản xuất chung: <b><a class="tnh-modal-attr" start_date="<?= $start_date ?>" end_date="<?= $end_date ?>" type_object="chi_phi_san_xuat_chung" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/costing/infoDetailCosting') ?>"><?= formatMoney($generalCosting) ?></a></b></div>
		<input type="hidden" name="direct_material" id="input" class="form-control" value="<?= $directMaterial ?>">
		<input type="hidden" name="direct_labor_costing" id="input" class="form-control" value="<?= $directLaborCosting ?>">
		<input type="hidden" name="general_costing" id="input" class="form-control" value="<?= $generalCosting ?>">
	</div>
</div>
<table id="tb-productions-orders" class="tnh-table table-bordered table-hover mtop10 dataTable">
	<thead>
		<tr>
			<th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
			<th><?= lang('tnh_product_code') ?></th>
			<th><?= lang('tnh_product_name') ?></th>
			<th class="text-center"><?= lang('tnh_quantity_finished') ?> MH</th>
			<th class="text-center"><?= lang('Chi phí NVL MH') ?></th>
			<th class="text-center"><?= lang('Chi phí SX chung MH') ?></th>
			<th class="text-center"><?= lang('Chi phí nhân công trực tiếp MH') ?></th>
			<th class="text-center"><?= lang('Chi phí NVL dở dang đầu kỳ') ?></th>
			<th class="text-center"><?= lang('Chi phí NVL dở dang cuối kỳ') ?></th>
			<th class="text-center"><?= lang('Giá thành đơn vị') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($products)) : ?>
			<?php foreach ($products as $key => $value) : ?>
				<?php
				$item_id = $value['item_id'];
				$soLuongHT = $value['quantity'];
				$chiPhiNVL = $this->costing_model->costingMaterialOfProduct($item_id, $start_date, $end_date)['total'];
				$traPheLieu = $this->costing_model->costingPurchaseInternal($item_id, $start_date, $end_date)['total'];
				$chiPhiNVL = $chiPhiNVL - $traPheLieu;

				$chiPhiNVLMatHang = !empty($chiPhiNVL) ? $chiPhiNVL : 0;
				$chiPhiNVLDoDangDK = 0;
				$chiPhiNVLDoDangCK = 0;

				if ($directMaterial == 0) {
					$chiPhiSXMatHang = 0;
					$chiPhiNCTTMatHang = 0;
				} else {
					//Công thức CP Sản xuất cho mặt hàng: (Chi phí sản xuất chung/Chi phí NVL trực tiếp) * CP NVL mặt hàng
					$chiPhiSXMatHang = ($generalCosting / $directMaterial) * $chiPhiNVLMatHang;
					//Công thức CP nhân công trực tiếp MH: (Chi phí nhân công trực tiếp/Chi phí NVL trực tiếp) * CP NVL mặt hàng
					$chiPhiNCTTMatHang = ($directLaborCosting / $directMaterial) * $chiPhiNVLMatHang;
				}

				//Tổng giá thành: $chiPhiNVLDoDangDK + ($chiPhiNVLMatHang + $chiPhiNCTTMatHang + $chiPhiSXMatHang) - $chiPhiNVLDoDangCK;
				$tongGiaThanh = $chiPhiNVLDoDangDK + ($chiPhiNVLMatHang + $chiPhiNCTTMatHang + $chiPhiSXMatHang) - $chiPhiNVLDoDangCK;

				//Giá thành đơn vị: $tongGiaThanh/$soLuongHT;
				$giaThanhDonVi = $tongGiaThanh / $soLuongHT;
				?>
				<tr>
					<?php if ($key == 0) : ?>
					<?php endif ?>
					<input type="hidden" name="pp_id[]" id="pp_id" class="form-control pp_id" value="<?= $value['pp_id'] ?>">
					<input type="hidden" name="type_item[]" id="type_item" class="form-control type_item" value="<?= $value['type_item'] ?>">
					<input type="hidden" name="product_id[]" id="product_id" class="form-control product_id" value="<?= $item_id ?>">
					<input type="hidden" name="soLuongHT[]" id="soLuongHT" class="form-control soLuongHT" value="<?= $soLuongHT ?>">
					<input type="hidden" name="chiPhiNVLMatHang[]" id="chiPhiNVLMatHang" class="form-control chiPhiNVLMatHang" value="<?= $chiPhiNVLMatHang ?>">
					<input type="hidden" name="chiPhiSXMatHang[]" id="chiPhiSXMatHang" class="form-control chiPhiSXMatHang" value="<?= $chiPhiSXMatHang ?>">
					<input type="hidden" name="chiPhiNCTTMatHang[]" id="chiPhiNCTTMatHang" class="form-control chiPhiNCTTMatHang" value="<?= $chiPhiNCTTMatHang ?>">

					<td class="text-center"><?= ++$key ?></td>
					<td><?= $value['item_code'] ?></td>
					<td><?= $value['item_name'] ?></td>
					<td class="text-center"><a class="tnh-modal-attr" start_date="<?= $start_date ?>" end_date="<?= $end_date ?>" type_object="so_luong_ht" item_id="<?= $item_id ?>" type_item="<?= $value['type_item'] ?>" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/costing/infoDetailCosting') ?>"><?= formatNumber($soLuongHT) ?></a></td>
					<td class="text-center"><a class="tnh-modal-attr" start_date="<?= $start_date ?>" end_date="<?= $end_date ?>" type_object="chi_phi_nvl_mh" item_id="<?= $item_id ?>" type_item="<?= $value['type_item'] ?>" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/costing/infoDetailCosting') ?>"><?= formatMoney($chiPhiNVLMatHang) ?></a></td>
					<td class="text-center"><?= formatMoney($chiPhiSXMatHang) ?></td>
					<td class="text-center"><?= formatMoney($chiPhiNCTTMatHang) ?></td>
					<td class="text-center"><input type="text" name="chiPhiNVLDoDangDK[]" style="width: 100%;" id="chiPhiNVLDoDangDK" class="form-control chiPhiNVLDoDangDK money-format" value="0"></td>
					<td class="text-center"><input type="text" name="chiPhiNVLDoDangCK[]" style="width: 100%;" id="chiPhiNVLDoDangCK" class="form-control chiPhiNVLDoDangCK money-format" value="0"></td>
					<td class="text-center td-gia-thanh-don-vi"><?= formatMoney($giaThanhDonVi) ?></td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</tfoot>
</table>
<script type="text/javascript">
	$(document).ready(function() {
		$('.chiPhiNVLDoDangDK, .chiPhiNVLDoDangCK').change(function(event) {
			trCurrent = $(this).closest('tr');
			soLuongHT = intVal(trCurrent.find('.soLuongHT').val());
			chiPhiNVLDoDangDK = intVal(trCurrent.find('.chiPhiNVLDoDangDK').val());
			chiPhiNVLDoDangCK = intVal(trCurrent.find('.chiPhiNVLDoDangCK').val());
			chiPhiNVLMatHang = intVal(trCurrent.find('.chiPhiNVLMatHang').val());
			chiPhiSXMatHang = intVal(trCurrent.find('.chiPhiSXMatHang').val());
			chiPhiNCTTMatHang = intVal(trCurrent.find('.chiPhiNCTTMatHang').val());

			tongGiaThanh = chiPhiNVLDoDangDK + (chiPhiNVLMatHang + chiPhiNCTTMatHang + chiPhiSXMatHang) - chiPhiNVLDoDangCK;
			giaThanhDonVi = tongGiaThanh / soLuongHT;

			trCurrent.find('.td-gia-thanh-don-vi').html(tnhFormatMoney(giaThanhDonVi));
		});
	});
</script>