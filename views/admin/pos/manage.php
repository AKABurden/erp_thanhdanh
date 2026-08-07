<?php init_not_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/style_pos.css');?>">
<?php echo form_open('clients/addPos', array('id' => 'add-pos')); ?>
<body>
	<div class="header">
		<div class="search_header">
			<i class="fa fa-search wap-icon-search-header"></i>
			<input type="text" class="wap-search-header" placeholder="Tìm mặt hàng">
			<div class="wap-login-client">
				<a href="<?=base_url('clients')?>" class="wap-go-back"><?=_l('order_tracking')?></a>
				<img src="<?=base_url()?><?=(!empty($contact_login->profile_image) ? 'uploads/client_profile_images/'.$contact_login->id.'/thumb_'.$contact_login->profile_image : 'assets/images/user-placeholder.jpg')?>">
				<span><?=(!empty($contact_login->firstname) ? $contact_login->firstname : '').' '.(!empty($contact_login->lastname) ? $contact_login->lastname : '')?></span>
			</div>
		</div>
		<div class="wap-container-search hide">
		</div>
	</div>
	<div class="wap-container">
		<div class="wap-content-left">
			<div class="wap-tab-items active pull-left" data-active="all_items">
				<span class="bold uppercase wap-title-left"><?=_l('all_items')?></span>
			</div>
			<div class="wap-tab-items pull-left" data-active="items_bought">
				<span class="bold uppercase wap-title-left"><?=_l('items_bought')?></span>
			</div>
			<span class="wap-filter-dont red pull-right"><?=_l('Bỏ lọc')?></span>
			<span class="wap-filter pull-right"><?=_l('filter_items')?></span>
			<div class="clearfix"></div>
			<div class="wap-line-header"></div>
			<!-- điều kiện lọc -->
			<div class="wap-content-filter">
				<div class="text-right">
					<i class="wap-close fa fa-times"></i>
				</div>
				<div class="bold uppercase wap-title-filter"><?=_l('filter_items')?></div>
				<table class="tnh-tb table-bordered table-hover m-group0 mtop10" style="table-layout: fixed;">
					<tbody>
						<tr>
							<td class="middle-table" style="width: 30%;">
								<span><?=_l('report_invoice_amount')?></span>
							</td>
							<td style="width: 70%; padding: 10px 10px; text-align: center;">
								<?=_l('Từ')?>
								<input type="text" class="wap-input filter-amount-from" onchange="formatNumBerKeyUp(this)">
								<?=_l('ticket_settings_to')?>
								<input type="text" class="wap-input filter-amount-to" onchange="formatNumBerKeyUp(this)">
							</td>
						</tr>
						<tr>
							<td class="middle-table">
								<span><?=_l('ch_categories')?></span>
							</td>
							<td>
								<?php echo render_select('category_id[]',$categories,array('id','category'),'','',array('multiple'=>true, 'data-actions-box'=>true)); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- end -->
			<div class="wap-tab-items-content active" id="all_items">
				<!-- danh sách hàng hóa -->
				<div class="wap-container-item">
					<?php foreach ($dataCategories as $keyCategories => $valueCategories) { ?>
						<div class="wap-categories pull-left">
							<div class="wap-categories-title pull-left"><?=$valueCategories['name_categories']?></div>
							<div class="wap-line-categories-title"></div>
							<div class="wap-line-categories-title"></div>
							<div class="wap-line-categories-title"></div>
							<div class="clearfix"></div>
							<div class="wap-line-categories"></div>
						</div>
						<div class="clearfix"></div>
						<?php foreach ($dataItem as $key => $value) { ?>
							<?php if($valueCategories['categories'] == $value['categories']) { ?>
								<div class="wap-item-content-left">
									<div class="wap-img-content-left">
										<?php if(empty($value['avatar'])) { ?>
											<img class="img-item" src="<?=base_url('uploads/no-img.jpg')?>">
										<?php } else { ?>
											<img class="img-item" src="<?=base_url().$value['avatar']?>">
										<?php } ?>
									</div>
									<div class="wap-content-item-left">
										<div>
											<span class="wap-italic"><?=_l('als_kb_groups')?>: </span><span class="wap-italic"><?=$value['name_categories']?></span>
										</div>
										<div>
											<span class="name-item"><?=$value['name']?></span> (<span><?=_l('code')?>: </span><span class="code-item"><?=$value['code']?></span>)
											<input type="hidden" class="id-item" value="<?=$value['id']?>">
										</div>
										<div>
											<span><?=_l('price')?>: </span><span class="price-item"><?=number_format($value['price'])?></span>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
					<?php } ?>
					<div class="clearfix"></div>
				</div>
				<!-- end -->
				<!-- phân trang -->
				<div class="wap-page">
					<?php for ($i=0; $i < $number_page; $i++) { ?>
						<div class="wap-number-page <?=($i == 0) ? 'active' : ''?>" data-number="<?=$i+1?>">
							<?=$i+1;?>
						</div>
					<?php } ?>
					<div class="clearfix"></div>
				</div>
				<!-- end -->
			</div>
			<div class="wap-tab-items-content" id="items_bought">
				<div class="wap-container-items-bought">
					<?php foreach ($items_bought as $key => $value) { ?>
						<div class="wap-item-content-left">
							<div class="wap-img-content-left">
								<img class="img-item" src="<?=base_url().$value['avatar']?>">
							</div>
							<div class="wap-content-item-left">
								<div>
									<span class="wap-italic"><?=_l('als_kb_groups')?>: </span><span class="wap-italic"><?=$value['name_categories']?></span>
								</div>
								<div>
									<span class="name-item"><?=$value['name']?></span> (<span><?=_l('code')?>: </span><span class="code-item"><?=$value['code']?></span>)
								</div>
								<div>
									<span><?=_l('price')?>: </span><span class="price-item"><?=number_format($value['price'])?></span>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="wap-content-right" style="position: relative;">
			<div class="tab-content-right">
				<div class="wap-tab pull-right" data-active="note">
					<?=_l('cong_note')?>
				</div>
				<div class="wap-tab pull-right active" data-active="detail">
					<?=_l('cong_lead_profile')?>
				</div>
				<div class="clearfix"></div>
			</div>
			<!-- tab thông tin -->
			<div class="wap-tab-content active" id="detail">
				<div class="wap-container-right">
					<div class="wap-time-right">
						<table class="tnh-tb table-bordered table-hover m-group0">
							<tbody>
								<tr>
									<td class="middle-table">
										<span><?=_l('date_create')?></span>
									</td>
									<td>
										<?php echo render_date_input('date_create','',date('d/m/Y')); ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="clearfix"></div>
					<div class="panel_s wap-border wap-overflow">
						<div class="wap-empty-table">
							<img src="<?=base_url('assets/images/table-no-data.png')?>">
						</div>
						<div class="panel-body">
							<div class="wap-content-item-right">
							</div>
						</div>
					</div>
				</div>
				<div class="wap-container-right-view" style="display: none;">
				</div>
			</div>
			<!-- end -->
			<!-- tab ghi chú -->
			<div class="wap-tab-content" id="note">
				<div class="mtop10">
					<?php echo render_textarea('note','','',array('rows'=>10)); ?>
				</div>
			</div>
			<!-- end -->
			<div class="wap-request">
				<div class="wap-btn-request">
					<!-- <div class="wap-btn"> --><button type="submit" class="wap-btn btn-save" style="width: 100%;" name="add" value="1"><?=_l('save')?></button><!-- </div> -->
					<button type="button" class="hwap-close btn-close-view" style="width: 100%; display: none;"><?=_l('close')?></button>
				</div>
				<div class="wap-statistical">
					<div class="wap-quantity-statistical">
						<div class="wap-title">
							<?=_l('item_quantity_all')?>
						</div>
						<div class="wap-number bold">
							0
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="wap-price-statistical">
						<div class="wap-title">
							<?=_l('tnh_total_amount')?>
						</div>
						<div class="wap-number bold">
							0
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</body>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script>
function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
    nStr += '';
    x = nStr.split(decSeperate);
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    }
    return x1 + x2;
}

function unformatNumber(nStr, decSeperate=".", groupSeperate=",") {
    return nStr.replace(/\,/g,'');
}

$(document).ready(function() {
    var opt = {
        format: 'Y-m-d',
        timepicker: false,
        scrollInput: false,
        lazyInit: true,
        dayOfWeekStart: 0,
    };
    $('#date_create').datetimepicker(opt);
    reHeight();
});


window.onresize = function() {reHeight();};
function reHeight() {
	//right
	var wap_overflow = document.getElementsByClassName("wap-overflow");
	var localtion_inPage  = wap_overflow[0].getBoundingClientRect();
	var height = document.body.offsetHeight;
	var height_wap_overflow = Number(height) - Number(localtion_inPage.top) - 100;
	document.getElementsByClassName("wap-overflow")[0].style.height = height_wap_overflow+'px';
	//left
	var wap_container_item = document.getElementsByClassName("wap-container-item");
	var localtion_inPage_container_item  = wap_container_item[0].getBoundingClientRect();
	var height_wap_container_item = Number(height) - Number(localtion_inPage_container_item.top) - 40;
	var height_wap_container_item_bought = Number(height) - Number(localtion_inPage_container_item.top) - 20;
	document.getElementsByClassName("wap-container-item")[0].style.height = height_wap_container_item+'px';
	document.getElementsByClassName("wap-container-items-bought")[0].style.height = height_wap_container_item_bought+'px';
}

$(".wap-search-header").keyup(function(e) {
	if($(this).val()) {
  		find_items($(this).val());
  	}
  	else {
  		$(".wap-container-search").addClass('hide');
  	}
});

function resetTotal_tr(trItem) {
	var amount = trItem.parents('.wap-p-items').find('.amount').val();
	var quantity = trItem.val();
	var total = Number(unformatNumber(amount)) * Number(quantity);
	trItem.parents('.wap-p-items').find('.total').text(formatNumber(total));
}

function resetTotal_all() {
	var content = $('.wap-content-item-right').find('.wap-p-items');
	var quantity = 0;
	var total = 0;
	$.each(content, function(i, v){
		quantity += Number($(this).find('.quantity').val());
		total += Number(unformatNumber($(this).find('.total').text()));
	});
	$('.wap-quantity-statistical').find('.wap-number').text(formatNumber(quantity));
	$('.wap-price-statistical').find('.wap-number').text(formatNumber(total));
}

function find_items(search) {
	var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
	var data = {};
	data['search'] = search;
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(site_url+'clients/getData_items', data).done(function(response){
    	response = JSON.parse(response);
    	var html = '';
    	$('.wap-container-search').html('');
    	if (response === undefined || response.length == 0) {
    		html = "<?=_l('not_results_found')?>";
    	}
    	$.each(response, function(i, v){
    		html += '<div class="wap-content-search">\
						<img class="img-item" src="<?=base_url()?>'+v.avatar+'">\
						<span class="name-item">'+v.name+'</span> (<span class="code-item">'+v.code+'</span>) - <span class="price-item">'+formatNumber(v.price)+'</span>\
						<input type="hidden" class="id-item" value="'+v.id+'">\
					</div>';
    	});
    	$('.wap-container-search').append(html);
    	$(".wap-container-search").removeClass('hide');
    });
}

$(document).on('click','.wap-content-search', function (e) {
	if($(this).find('img').length > 0) {
  		add_items($(this));
  	}
});

$(document).on('click','.wap-item-content-left', function (e) {
	if($(this).find('img').length > 0) {
  		add_items($(this));
  	}
});

$(document).on('keyup','.quantity', function (e) {
	resetTotal_tr($(this));
	resetTotal_all();
});

var ii = 0;
function add_items(trItem) {
	$('.wap-empty-table').addClass('hide');
	alert_float('success',"<?=_l('ch_items_import_success')?>");
	var id_item = trItem.find('.id-item').val();
	if($('.wap-content-item-right').find('.IDitem[value='+id_item+']').length > 0) {
		var quantity = Number($('.wap-content-item-right').find('.IDitem[value='+id_item+']').parents('.wap-p-items').find('.quantity').val());
		var quantity = quantity + 1;
		$('.wap-content-item-right').find('.IDitem[value='+id_item+']').parents('.wap-p-items').find('.quantity').val(quantity);
		resetTotal_tr($('.wap-content-item-right').find('.IDitem[value='+id_item+']').parents('.wap-p-items').find('.quantity'));
	}
	else {
		var trItem_html = '<div class="wap-p-items" data-ii="'+ii+'">\
								<div class="wap-remove pull-left">\
									<i class="fa fa-times"></i>\
								</div>\
								<div class="wap-img pull-left">\
									<img src="'+trItem.find('.img-item').attr('src')+'">\
								</div>\
								<div class="wap-name-item pull-left bold">\
									'+trItem.find('.name-item').text()+' ('+trItem.find('.code-item').text()+')\
									<input type="hidden" name="item_id[]" class="IDitem" value="'+trItem.find('.id-item').val()+'">\
								</div>\
								<div class="wap-price-item pull-left">\
									<input class="amount" type="text" value="'+trItem.find('.price-item').text()+'" readonly>\
								</div>\
								<div class="wap-quantity-item pull-left">\
									<input class="quantity" name="quantity[]" type="number" value="1">\
								</div>\
								<div class="wap-total-item pull-left">\
									<span class="total bold">'+trItem.find('.price-item').text()+'</span>\
								</div>\
								<div class="clearfix"></div>\
							</div>\
							<div class="wap-line" data-ii="'+ii+'"></div>';
		$('.wap-content-item-right').prepend(trItem_html);
		ii++;
	}
	resetTotal_all();
}

$(document).on('click','.wap-remove', function (e) {
	var data_ii = $(this).parents('.wap-p-items').attr('data-ii');
	$(this).parents('.wap-content-item-right').find('div[class=wap-line][data-ii='+data_ii+']').remove();
	$(this).parents('.wap-content-item-right').find('div[class=wap-p-items][data-ii='+data_ii+']').remove();
	if($('.wap-content-item-right').find('.wap-p-items').length == 0) {
		$('.wap-empty-table').removeClass('hide');
	}
	resetTotal_all();
});

$(document).on('click','.wap-tab', function (e) {
	var data_active = $(this).attr('data-active');
	$('.wap-tab').removeClass('active');
	$(this).addClass('active');
	$('.wap-tab-content').removeClass('active');
	$('div[class=wap-tab-content][id='+data_active+']').addClass('active');
});
$(document).on('click','.wap-tab-items', function (e) {
	var data_active = $(this).attr('data-active');
	$('.wap-tab-items').removeClass('active');
	$(this).addClass('active');
	$('.wap-tab-items-content').removeClass('active');
	$('div[class=wap-tab-items-content][id='+data_active+']').addClass('active');
});

$(document).on('click','.wap-filter', function (e) {
	if($('.wap-content-filter').hasClass('active')) {
		$('.wap-content-filter').removeClass('active');
	}
	else {
		$('.wap-content-filter').addClass('active');
	}
});
$(document).on('click','.wap-filter-dont', function (e) {
	$('.filter-amount-from').val('');
	$('.filter-amount-to').val('');
	$('select[name="category_id[]"]').selectpicker('val',-1);
	$('select[name="category_id[]"]').trigger('change');
});

$(document).on('click','.wap-close', function (e) {
	$('.wap-filter').trigger('click');
});

function filter_item() {
 	$('select[name="category_id[]"]').trigger('change');
}

var check_true = true;
$('select[name="category_id[]"]').change(function(e){
	var amount_from = $('.filter-amount-from').val();
	var amount_to = $('.filter-amount-to').val();
    var arr_id = $(this).val();
    // console.log(arr_id);
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
	var data = {};
	data['amount_from'] = amount_from;
	data['amount_to'] = amount_to;
	data['arr_id'] = arr_id;
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(site_url+'clients/getData_filter_items', data).done(function(response){
    	response = JSON.parse(response);
    	var html = '';
    	var html_page = '';
    	$('.wap-container-item').html('');
    	$('.wap-page').html('');
    	$.each(response.dataCategories, function(i_key, v_key){
    		html += '<div class="wap-categories pull-left">\
						<div class="wap-categories-title pull-left">'+v_key.name_categories+'</div>\
						<div class="wap-line-categories-title"></div>\
						<div class="wap-line-categories-title"></div>\
						<div class="wap-line-categories-title"></div>\
						<div class="clearfix"></div>\
						<div class="wap-line-categories"></div>\
					</div>\
					<div class="clearfix"></div>';
			check_true = true;
			var array_id = [];
			$.each(response.dataItem, function(i_sub_key, v_sub_key){
				for (var i = 0; i < array_id.length; i++){
				    if(v_sub_key.id == array_id[i]) {
				    	check_true = false;
				    }
				}

				if(check_true === true && v_sub_key.categories == v_key.categories) {
					html += '<div class="wap-item-content-left">\
								<div class="wap-img-content-left">\
									<img class="img-item" src="<?=base_url()?>'+v_sub_key.avatar+'">\
								</div>\
								<div class="wap-content-item-left">\
									<div>\
										<span class="wap-italic">Nhóm: </span><span class="wap-italic">'+v_sub_key.name_categories+'</span>\
									</div>\
									<div>\
										<span class="name-item">'+v_sub_key.name+'</span> (<span><?=_l('code')?>: </span><span class="code-item">'+v_sub_key.code+'</span>)\
										<input type="hidden" class="id-item" value="'+v_sub_key.id+'">\
									</div>\
									<div>\
										<span><?=_l('price')?>: </span><span class="price-item">'+formatNumber(v_sub_key.price)+'</span>\
									</div>\
								</div>\
							</div>';
					array_id.push(v_sub_key.id);
				}
			});
    	});
    	
    	for(var i = 0; i < response.number_page; i++) {
    		var i_number_page = i+1;
    		var active = '';
    		if(i == 0) {
    			active = 'active';
    		}
    		html_page += '<div class="wap-number-page '+active+'" data-number="'+i_number_page+'">\
    						'+i_number_page+'\
						</div>';
    	}
    	html_page += '<div class="clearfix"></div>';
    	$('.wap-page').append(html_page);
    	$('.wap-container-item').prepend(html);
    });
});

$(document).on('change','.filter-amount-from', function (e) {
	filter_item();
});

$(document).on('change','.filter-amount-to', function (e) {
	filter_item();
});

$(document).on('click','.wap-number-page', function (e) {
	if(!$(this).hasClass('active')) {
		$('.wap-number-page').removeClass('active');
		$(this).addClass('active');

		var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
		var data = {};
		data['number_page'] = $(this).attr('data-number');
	    if (typeof(csrfData) !== 'undefined') {
	      data[csrfData['token_name']] = csrfData['hash'];
	    }
	    $.post(site_url+'clients/getData_number_page', data).done(function(response){
	    	response = JSON.parse(response);
	    	var html = '';
	    	$('.wap-container-item').html('');
	    	$.each(response.dataCategories, function(i_key, v_key){
	    		html += '<div class="wap-categories pull-left">\
							<div class="wap-categories-title pull-left">'+v_key.name_categories+'</div>\
							<div class="wap-line-categories-title"></div>\
							<div class="wap-line-categories-title"></div>\
							<div class="wap-line-categories-title"></div>\
							<div class="clearfix"></div>\
							<div class="wap-line-categories"></div>\
						</div>\
						<div class="clearfix"></div>';
				check_true = true;
				var array_id = [];
				$.each(response.dataItem, function(i_sub_key, v_sub_key){
					for (var i = 0; i < array_id.length; i++){
					    if(v_sub_key.id == array_id[i]) {
					    	check_true = false;
					    }
					}

					if(check_true === true && v_sub_key.categories == v_key.categories) {
						html += '<div class="wap-item-content-left">\
									<div class="wap-img-content-left">\
										<img class="img-item" src="<?=base_url()?>'+v_sub_key.avatar+'">\
									</div>\
									<div class="wap-content-item-left">\
										<div>\
											<span class="wap-italic">Nhóm: </span><span class="wap-italic">'+v_sub_key.name_categories+'</span>\
										</div>\
										<div>\
											<span class="name-item">'+v_sub_key.name+'</span> (<span><?=_l('code')?>: </span><span class="code-item">'+v_sub_key.code+'</span>)\
											<input type="hidden" class="id-item" value="'+v_sub_key.id+'">\
										</div>\
										<div>\
											<span><?=_l('price')?>: </span><span class="price-item">'+formatNumber(v_sub_key.price)+'</span>\
										</div>\
									</div>\
								</div>';
						array_id.push(v_sub_key.id);
					}
				});
	    	});
	    	$('.wap-container-item').prepend(html);
	    });
	}
});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		appValidateForm($('#add-pos'), {
        date: 'required',
    }, db);

    function db(form) {
    	$('.add').attr('disabled', 'disabled');
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }
        var url = form.action;
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        $.ajax({
        	url : url,
        	type : 'POST',
        	dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
        	data: formData,
        })
        .done(function(data) {
        	if (data.result) {
        		alert_float('success', data.message);
        		$('.wap-content-item-right').html('');
        		$('.wap-container-right').hide();

        		$('.wap-container-right-view').show();
        		$('.wap-container-right-view').html(data.view);

        		$('.btn-save').hide();
        		$('.btn-close-view').show();
        		// window.location.href = site.base_url+'admin/orders';
        	} else {
        		alert_float('danger', data.message);
        		// $('.add').removeAttr('disabled', 'disabled');
        	}
        })
        .fail(function() {
            alert_float('danger', '<?= lang('tnh_error_please_reload_page') ?>');
        	$('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }

    $('.btn-close-view').click(function(event) {
    	$('.wap-container-right-view').hide();
    	$('.wap-container-right').show();
    	$('.btn-save').show();
    	$('.btn-close-view').hide();
    });
});
</script>
</html>

