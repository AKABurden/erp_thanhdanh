<style>
	.wrap-table {
		margin-top: 25px;
		width: 100%;
    	max-width: 100%;
    	margin-bottom: 20px;
	}
	.wrap-table thead th, .wrap-table tbody td {
		border: 1px solid #a9a9a9;
		padding: 10px;
		vertical-align: middle;
	}
	.title-td {
		font-size: 16px;
	    font-weight: 500;
	    text-transform: uppercase;
	    background: #ffe7a3;
	}
	.colum_new {
		background: #e0ffff;
	}
</style>
<div class="content">
	<div class="col-md-6">
		<label><?= _l('choise_set_price'); ?></label>
	    <select class="select_prices" multiple="multiple" style="width: 100%;">
	    	<?php foreach ($allData as $key => $value) { ?>
		        <option value="<?= $value['id'] ?>" data-id="<?= $value['id'] ?>"><?= $value['name'] ?></option>
	    	<?php } ?>
	    </select>
	</div>
	<div class="col-md-6">
		<label>Tìm kiếm (Mã hàng/ Tên hàng)</label>
		<input type="text" class="form-control search_item" value="" aria-invalid="false" autocomplete="off">
	</div>
	<div class="clearfix"></div>
    <hr />
    <table class="table-hover wrap-table table-item-prices">
    	<thead>
    		<tr>
    			<th class="text-left bold"><?= _l('code_item') ?></th>
    			<th class="text-left bold"><?= _l('name_item') ?></th>
    			<th class="text-left bold"><?= _l('item_price_buy') ?></th>
    			<th class="text-left bold"><?= _l('item_price_last') ?></th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php $key_type = 1; ?>
    		<?php foreach ($allDataItem as $key => $value) { ?>
    			<?php
                    if($value['type'] == 'product') {
                        $key_type++;
                    }
                ?>
                <?php if($key == 0 && $key_type == 1) { ?>
                	<tr class="title-tr">
                		<td class="text-left title-td" colspan="4">
                			hàng hóa
                		</td>
                	</tr>
                <?php } else if($key_type == 2) { ?>
                	<tr class="title-tr">
                		<td class="text-left title-td" colspan="4">
                			thành phẩm
                		</td>
                	</tr>
                <?php } ?>
    			<tr data-id="<?= $value['id'] ?>" data-name="<?= $value['name'] ?>" data-code="<?= $value['code'] ?>">
	    			<td class="text-left">
	    				<?= $value['code'] ?>
	    			</td>
	    			<td class="text-left"><?= $value['name'] ?></td>
	    			<td class="text-right"><?= number_format($value['price_import']) ?></td>
	    			<td class="text-right"><?= number_format($value['last_price']) ?></td>
	    		</tr>
    		<?php } ?>
    	</tbody>
    </table>
</div>

<script>
$(function(){
	$('.select_prices').select2();
});

var arrVal = [];
var arrID_exists = [];
$('.select_prices').change(function(e) {
	var val = $(this).val();
	//kiểm tra thêm mới
	if(val) {
		for (var i = 0; i < val.length; i++) {
			if($('.table-item-prices').find('th[data-check="'+val[i]+'"]').length == 0) {
				arrVal.push(val[i]);
			}
		}
	}
	//end
	//kiểm tra xóa
	var checkTrue = false;
	if(val) {
		for (var j = 0; j < arrVal.length; j++) {
			if(val.indexOf(arrVal[j]) == -1) {
				checkTrue = true;
				var idRemove = arrVal[j];

				arrVal.splice(j, 1);
				arrID_exists.splice(j, 1);
				break;
			}
		}
	}

	if(checkTrue == true) {
		$('.table-item-prices').find('th[data-check="'+idRemove+'"]').remove();
		$('.table-item-prices').find('td[data-check="'+idRemove+'"]').remove();
		var title_td = $('.title-td');
    	$.each(title_td, function(i, v){
    		var colspan = $(v).attr('colspan');
    		colspan = Number(colspan) - 1;
    		$(v).attr('colspan', colspan);
    	});
	}
	//end

	var data = {};
    if (typeof(csrfData) !== 'undefined') {
      	data[csrfData['token_name']] = csrfData['hash'];
    }
    data['arrID'] = arrVal;
    data['arrID_exists'] = arrID_exists;
    $.post(admin_url+'set_prices/getRow', data).done(function(response){
    	response = JSON.parse(response);
    	if(response.html_thead != '') {
	    	var title_td = $('.title-td');
	    	$.each(title_td, function(i, v){
	    		var colspan = $(v).attr('colspan');
	    		colspan = Number(colspan) + 1;
	    		$(v).attr('colspan', colspan);
	    	});

	    	$('.table-item-prices').find('thead tr').append(response.html_thead);

	    	$.each(response.html_tbody, function(i, v){
	    		if($('.table-item-prices').find('tr[data-id="'+v.id+'"]').length > 0) {
	    			$('.table-item-prices').find('tr[data-id="'+v.id+'"]').append(v.price);
	    		}
	    	});

	    	arrID_exists.push(response.id_exists);
	    }
    });
});

$('.search_item').change(function(e) {
	var val = $(this).val();
	var allTd = $('.table-item-prices').find('tbody tr:not(.title-tr)');
	if(val) {
		$.each(allTd, function(i, v){
			if($(v).attr('data-name').search(val) >= 0) {
				$(v).removeClass('hide');
			} else if($(v).attr('data-code').search(val) >= 0) {
				$(v).removeClass('hide');
			} else {
				$(v).addClass('hide');
			}
		});
	} else {
		$.each(allTd, function(i, v){
			$(v).removeClass('hide');
		});
	}
});

$('body').on('change', '.event-change', function(e) {
	var val = $(this).val();
	var id_setPrice = $(this).parents('td').attr('data-check');
	var id_item = $(this).attr('data-id');

	var data = {};
    if (typeof(csrfData) !== 'undefined') {
      	data[csrfData['token_name']] = csrfData['hash'];
    }
    data['val'] = val;
    data['id_setPrice'] = id_setPrice;
    data['id_item'] = id_item;
    $(this).val(formatNumber(val));
    $.post(admin_url+'set_prices/setRow', data).done(function(response){
    	alert_float('success', 'Cập nhật thành công!');
    });
});

</script>