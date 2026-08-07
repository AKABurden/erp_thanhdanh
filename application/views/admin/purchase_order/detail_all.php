<?php init_head(); ?>
<style type="text/css">
  .item-purchase .ui-sortable tr td input {
    width: 80px;
  }
  .select2-choice {
        height: 100% !important;
    }
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(), array('id' => 'supplier_quotes-form', 'class' => '_transaction_form invoice-form'));
			if (isset($items)) {
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<div class="panel_s">
				 	<div class="additional"></div>
				 	<div class="panel-body">
				 	<?php
						$type = '';
						if (!isset($items))
							$type = 'warning';
						elseif ($items->status == 0)
							$type = 'warning';
						elseif ($items->status == 1)
							$type = 'info';
						elseif ($items->status == 2)
							$type = 'success';

						?>
				 		<div class="ribbon <?= $type ?>" project-status-ribbon-2="">
				 			<?php
								if (isset($items))
									{
									$status = format_purchase_status($items->status, '', false);
								}
								else
									{
									$status = format_purchase_status(-1, '', false);
								}
								?>
				 			<span><?= $status ?></span>
						</div>
						<?php if(isset($items)) { ?>
						<?php } ?>
						<h4 class=" bold no-margin font-medium">
						    <?php echo _l('ch_po_t'); ?>
						</h4>
						<hr />
				 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-warning text-center total_debt hide"></div>
				 			<div class="panel panel-primary">
								<div class="panel-heading"><?=_l('lead_general_info')?></div>
								<div class="panel-body">
									<?php if(isset($items)) {
										echo format_purchase_order_father_all($items->id);
									?>
									<br>
									<br>
									<?php } ?>
									<table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
										<tbody>
											<tr>
												<td>
													<label for="number" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('ch_code_p'); ?>
													</label>
												</td>
												<td>
													<div class="form-group">
										                <div class="input-group">
										                  	<span class="input-group-addon">
										                    <?php echo (isset($items) ? ($items->prefix) : get_option('prefix_purchase_order'));?>-</span>
															<?php
																	$number = sprintf('%06d', ch_getMaxID('id', 'tblpurchase_order') + 1);
																	$value = (isset($items) ? ($items->code) : $number);
																?>
										                    <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
										                </div>
									                </div>
												</td>
												<td>
													<label for="date" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('ch_date_p'); ?>
													</label>
												</td>
												<td>
													<?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
			                  						<?php echo render_date_input('date', '', $value); ?>
												</td>
											</tr>
											<tr>
												<td>
													<label for="suppliers_id" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('supplier'); ?>
													</label>
												</td>
												<td>
													<?php
														$value = (isset($items) ? $items->suppliers_id : '');
														echo render_select('suppliers_id',$suppliers,array('id','company'),'',$value);
													?>
												</td>
												<td>
													<label for="id_staff" class="control-label">
														<?php echo _l('als_staff'); ?>
													</label>
												</td>
												<td>
													<?php $value = (isset($items) ? $items->staff_create : get_staff_user_id()); ?>
			                  						<?php echo render_select('id_staff', $staff, array('staffid', array('firstname', 'lastname')),'',$value,array('disabled'=>true)); ?>
												</td>
											</tr>
											<tr>
												<td>
													<label for="type_items" class="control-label">
														<?php echo _l('ch_type'); ?>
													</label>
												</td>
												<td >
													<?php
											         	echo render_select('type_items', $type_items, array('type', 'name'),'',-1);
													?>
												</td>
												<td>
													<label for="type_items" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('ch_delivery_date'); ?>
													</label>
												</td>
												<td >
													<?php $delivery_date = (isset($items) ? _d($items->delivery_date) : ''); ?>
			                  						<?php echo render_date_input('delivery_date', '', $delivery_date); ?>
												</td>
											</tr>
											<tr>
												<td rowspan="2">
													<label for="note" class="control-label">
														<?php echo _l('ch_note_t'); ?>
													</label>
												</td>
												<td rowspan="2">
													<?php
														$value = (isset($items) ? $items->note : "");
														echo render_textarea('note', '', $value);
													?>
												</td>
												<td>
													<label for="delivery_cost" class="control-label">
														<?php echo _l('ch_delivery_cost'); ?>
													</label>
												</td>
												<td >
													<?php
														$value = (isset($items) ? number_format($items->delivery_cost) : 0);
														echo render_input('delivery_cost', '', $value);
													?>
												</td>
											</tr>
											<tr>
												<td>
													<label for="reduce_cost" class="control-label">
														<?php echo _l('ch_reduce_cost'); ?>
													</label>
												</td>
												<td >
													<?php
														$value = (isset($items) ? number_format($items->reduce_cost) : 0);
														echo render_input('reduce_cost', '', $value);
													?>
												</td>
											</tr>
										</tbody>
									</table>
					           </div>
					       </div>
						</div>
						<div class="clearfix"></div>
						<?php
		                    $customer_custom_fields = false;
		                    if(total_rows(db_prefix().'customfields',array('fieldto'=>'purchase_order','active'=>1)) > 0 ){
		                         $customer_custom_fields = true;
		                ?>
		                <?php } ?>
		                <?php if($customer_custom_fields) { ?>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				 			<div class="panel panel-info">
								<div class="panel-heading"><?=_l('custom_fields')?></div>
								<div class="panel-body">

				                   	<div role="tabpanel" class="tab-pane" id="custom_fields">
				                    	<?php $value_id = (isset($items) ? $items->id : '');?>
				                      	<?php echo render_custom_fields('purchase_order',$value_id); ?>
				                   	</div>

					           </div>
					       </div>
						</div>
						<?php } ?>
						<div class="clearfix"></div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mbot50">
							<div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
							<div class="panel-heading">
								<?= lang('tnh_info_items') ?>
							</div>
							<div class="panel-body">
									<div class="table-responsive">
									<table style="table-layout: fixed;" class="dt-tnh table item-supplier_quotes table-bordered table-hover">
										<thead>
											<tr>
												<th style="width: 100px;" class="center"><?=_l('image')?><input type="hidden" id="itemID" value="" /></th>
													<th style="width: 200px;" class="text-left"><?php echo _l('ch_items_name_t'); ?></th>
													<th class="hide"><?=_l('code_item_in_invoice')?></th>
													<th class="hide"><?php echo _l('status_item_in_internal'); ?></th>
													<th class="hide"><?php echo _l('status_item_in_suppliers'); ?></th>
													<th style="width: 100px;" class="text-center hide"><?php echo _l('item_quantity'); ?>
													<th style="width: 100px;" class="text-center"><?php echo _l('item_quantity'); ?></th>
													<th style="width: 100px;" class="text-center"><?php echo _l('quantity_inventory_missing'); ?></th>
													<th style="width: 100px;" class="text-left"><?php echo _l('item_unit'); ?></th>
													<th style="width: 100px;" class="text-right hide"><?php echo _l('price_expected'); ?></th>
													<th style="width: 100px;" class="text-right"><?php echo _l('ch_price_ncc'); ?></th>
													<th style="width: 100px;" class="text-center"><?php echo _l('tax'); ?></th>
													<th style="width: 100px;" class="text-right hide"><?php echo _l('amount_expected_vnd'); ?></th>
													<th style="width: 100px;" class="text-right"><?php echo _l('promotion_suppliers'); ?></th>
													<th style="width: 100px;" class="text-right"><?php echo _l('amount_suppliers_vnd'); ?></th>
													<th style="width: 150px;"class="text-left"><?php echo _l('note'); ?></th>
													<th style="width: 30px;"></th>
											</tr>
										</thead>
										<tbody>
										<?php
                                            $i=0;
                                            $totalQuantity_approve=0;
                                            $totalQuantity=0;
                                            $vat=0;
                                            if(isset($items) && count($items->items) > 0) {
                                                foreach($items->items as $value) {
                                                	$total_novat = $value['quantity']*$value['unit_cost'];
                                                	$total = $value['quantity']*$value['unit_cost']*(1+($value['tax_rate']/100));
                                                    ?>
                                                    <tr class="sortable item">
	                                                    <td class="dragger" style="text-align: center;">
	                                                    	<img style="width: 4em;height: 4em;" src="<?=$value['avatar']?>">
	                                                    	<?=format_item_purchases($value['type'])?>
	                                                    	<input type="hidden" name="items[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>" />
		                                                    <input type="hidden" name="items[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>">
	                                                    </td>
	                                                    <td>
							                            
							                            <select class="selectpicker custom_item_select" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									                        	<?php foreach(get_options_search_cbo('items',$value['product_id'],$value['type']) as $t) { ?>
									                           		<option value="<?php echo $t['id']; ?>" data-idd="<?=$t['quantity_warehoue']?>" <?php echo($t['id'] == $value['id'] ? 'selected' : '') ?>> <?=$t['name']?> </option>
									                           	<?php } ?>
									                    </select>
									                    <br><br>
									                    <div class="color"><?=format_item_color($value['product_id'],$value['type'])?></div>
												    	</td>
	                                                    <td class="hide">???</td>
	                                                    <td class="hide">???</td>
	                                                    <td class="hide">???</td>

	                                                    <td class="hide"><input readonly style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input mainQuantity" type="text" name="items[<?php echo $i; ?>][quantity]" value="<?=$value['quantity']?>" /></td>
        												<td ><input readonly style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input mainQuantity_suppliers" type="text" name="items[<?php echo $i; ?>][quantity_suppliers]" value="<?=$value['quantity_suppliers']?>" /></td>
        												<td class="center quantity_warehoue"></td>
        												<td><?=$value['unit']?></td>
        												<td class="hide"><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_expected" type="text" name="items[<?php echo $i; ?>][price_expected]" value="<?=number_format($value['price_expected'])?>" /></td>
        												<td ><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_suppliers" type="text" name="items[<?php echo $i; ?>][price_suppliers]" value="<?=number_format($value['price_suppliers'])?>" /></td>
        												<td>
        													<select class="selectpicker tax" name="items[<?php echo $i; ?>][tax_id]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        														<option value data-taxrate="0"><?=_l('no_tax')?></option>
									                        	<?php foreach($tax as $t) { ?>
									                           		<option value="<?php echo $t['id']; ?>" data-taxrate="<?=$t['taxrate']?>" <?php echo($t['id'] == $value['tax_id'] ? 'selected' : '') ?>> <?=$t['name']?> </option>
									                           	<?php } ?>
									                        </select>
        													<input type="hidden" class="tax_rate" name="items[<?php echo $i; ?>][tax_rate]" value="<?=$value['tax_rate']?>">
        												</td>
        												<td class="hide align_right total_expected"><?=number_format($value['total_expected'])?></td>
        												<td ><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input align_right promotion_expected" type="text" name="items[<?php echo $i; ?>][promotion_expected]" value="<?=number_format($value['promotion_expected'])?>" /></td>

        												<td class="align_right total_suppliers"><?=number_format($value['total_suppliers'])?></td>
        												<td><textarea class="note" name="items[<?php echo $i; ?>][note]" value="<?=$value['note']?>"><?=$value['note']?></textarea></td>
	                                                    <td></td>
	                                                </tr>
                                            	<?php
                                            		$i++;
                                            		$totalQuantity+=$value['quantity'];
                                        		} ?>
                                        	<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
				 		</div>
				 	</div>
			 	</div>
				<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
					<div style="width: 80%" class=" pull-left">
						<table class="table tnh-tb noMargin table-color_sum dont-responsive-table" style="table-layout: fixed;">
							<tbody>
								<tr>
									<!-- thống kê ncc -->
									<td style="width: 8%;">
										<span class="bold"><?php echo _l('quantity'); ?>:</span><br>
										<span class="bold"><?php echo _l('amount'); ?>:</span>
									</td>
									<td style="width: 6%;">
										<span class="total_quantity_suppliers"><?php echo $totalQuantity ?></span><br>
										<span class="total_sub_suppliers">0</span>
									</td>
									<td style="width: 6%;">
										<span class="bold"><?php echo _l('ch_tax_all'); ?> :</span>
									</td>
									<td style="width: 10%;">
										<?php $tax_all = (isset($items) ? $items->tax_all : '');?>
										<?=str_replace(' tax',' tax_all',get_taxes_dropdown_template('tax_all',$tax_all))?>

									</td>
									<td style="width: 13%;">
										<div>
											<div>
												<input type="text" name="valtype_check_suppliers" value="<?=((isset($items)) ? $items->valtype_check_suppliers : 1)?>" class="hide" id="valtype_check_suppliers">
												<div  style="float: left;"  class="radio radio-primary radio-inline"><input type="radio" class="type_check_suppliers" name="type_check_suppliers" value="1" <?=(isset($items)?((isset($items)&&$items->valtype_check_suppliers == 1) ? 'checked' : ''): 'checked')?>><label>%</label></div>
												<div  class="radio radio-primary radio-inline">
													<input type="radio"  name="type_check_suppliers" class="type_check_suppliers" value="2" <?=((isset($items)&&$items->valtype_check_suppliers == 2) ? 'checked' : '')?>>
													<label>Tiền</label>
												</div>
											</div>
											<div style="display: flex; align-items: center; float: left;">
												<span style="float: left;width: 45%;" class="bold"><?php echo _l('Chiết khấu'); ?>:&nbsp;&nbsp;</span>
												<input placeholder="Chiết khấu"  class="form-control" id="discount_percent_suppliers" value="<?=(isset($items) ? $items->discount_percent_suppliers : 0)?>" name="discount_percent_suppliers" style="width: 100%;float: left;" onkeyup="formatNumBerKeyUp(this)">
											</div>
											<div class="clearfix"></div>
										</div>
									</td>
									<td class="discount_percent_total_suppliers" style="width: 6%;">
										0
									</td>

									<td style="width: 8%;">
										<span style="color:red" class="bold"><?php echo _l('payment_total_amount'); ?> (<?php echo _l('ch__ncc'); ?>):</span>
									</td>
									<td style="color:red; width: 7%;" class="totalPrice_suppliers">
										0
									</td>
								</tr>
							</tbody>
						</table>
					</div>
		            <button class="btn btn-info pull-right form-submitersss" style="padding: 28px;"><?=_l('submit')?>
		            </button>
		            <a href="<?=admin_url('purchase_order')?>" class="btn btn-default pull-right" style="padding: 28px;margin-right: 10px"><?=_l('go_back')?>
		            </a>
		        </div>
			</div>

			<?php echo form_close(); ?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
	getTotalPrice();
	$('#reduce_cost').on('change', (e)=>{
            key="";
            money=$('#reduce_cost').val().replace(/[^\-\d\.]/g, '');
            a=money.split(".");
            $.each(a , function (index, value){
                key=key+value;
            });
            if(key == '')
            {
            	key = 0;
            }
            $('#reduce_cost').val(formatNumber(key, '.', ','));
            getTotalPrice();
    });
	$('#delivery_cost').on('change', (e)=>{
            key="";
            money=$('#delivery_cost').val().replace(/[^\-\d\.]/g, '');
            a=money.split(".");
            $.each(a , function (index, value){
                key=key+value;
            });
            if(key == '')
            {
            	key = 0;
            }
            $('#delivery_cost').val(formatNumber(key, '.', ','));
            getTotalPrice();
    });
	$(function(){
		var dt = $('.item-supplier_quotes').DataTable({
			'searching': false,
			'ordering': false,
			'paging': false,
	        "info": false,
	        'fixedHeader': true,
	        // scrollY: true,
			// scrollY: '150px',
			// scrollX: true,
	        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
	        },
	        "initComplete": function(settings, json) {
	            var t = this;
	            t.parents('.table-loading').removeClass('table-loading');
	            t.removeClass('dt-table-loading');
	            mainWrapperHeightFix();
	        },
		});
	});
	$(document).on('change', '#suppliers_id', (e)=>{
    	var currentQuantityInput = $(e.currentTarget);

		var id = $(currentQuantityInput).val();
		if(id == '') {
			$('.total_debt').addClass('hide');
		}
		else {
			$.post(admin_url + 'suppliers/get_debt/'+id,{[csrfData['token_name']] : csrfData['hash']}, function(data){3
				data = JSON.parse(data);
				if(data.success == true)
				{
					$('.total_debt').removeClass('hide');
					$('.total_debt').html('<?=_l('ch_wanring_debt_limit')?><b>'+data.total+'</b>');
				}
				else
				{
					$('.total_debt').addClass('hide');
				}
		    });
		}
	});
	$(function(){
		_validate_form($('#supplier_quotes-form'), {
        date: "required",
        number: "required",
        delivery_date: "required",
        suppliers_id: "required",
    });
	});
		function init_ajax_searchs(e, t, a, i) {
		    var n = $("body").find(t);
		    var h = t;
		    if (n.length) {
		        var s = {
		            ajax: {
		                url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
		                data: function() {
		                    var type = $('#type_items').val();
		                    var t = {[csrfData.token_name] : csrfData.hash};
		                    return t.type = e, t.rel_id = "", t.q = "{{{q}}}",t.type_items = type, void 0 !== a && jQuery.extend(t, a), t
		                }
		            },
		            locale: {
		                emptyTitle: app.lang.search_ajax_empty,
		                statusInitialized: app.lang.search_ajax_initialized,
		                statusSearching: app.lang.search_ajax_searching,
		                statusNoResults: app.lang.not_results_found,
		                searchPlaceholder: app.lang.search_ajax_placeholder,
		                currentlySelected: app.lang.currently_selected
		            },
		            requestDelay: 500,
		            cache: !1,
		            preprocessData: function(e) {
		                for (var t = [], a = e.length, i = 0; i < a; i++) {
		                    var n = {
		                        value: e[i].id,
		                        text: e[i].name,
		                        type_items: e[i].type_items,
		                        quantity_warehoue: e[i].quantity_warehoue
		                    }; t.push(n)
		                }
		                findItemasdsad(t,h);
		            },
		            preserveSelectedPosition: "after",
		            preserveSelected: !0
		        };
		        n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s);

		    }
		}
	var findItemasdsad = (data,h) => {
		setTimeout(function(){
			$(h).find('option:gt(0)').remove();
		    $(h).selectpicker('refresh');
		    var count = data.length;
		    var html ='';
		    $.each(data, function(key,value){
		    	if(key == 0)
		    	{
		    	html +='<optgroup label="'+value.text+'">';
		    	}else if(value.value == 'h')
		    	{
		    		html +='</optgroup>';
		    		html +='<optgroup label="'+value.text+'">';
		    	}else{
			    html +='<option data-idd="'+value.quantity_warehoue+'" data-id='+value.type_items+' value="' + value.value + '">'  + value.text + '</option>';
				}
			});
			html +='</optgroup>';
			$(h).html(html);
			$(h).selectpicker('refresh');
			if(count > 3)
			{
				$(h).parents().find('.status').addClass('hide');
			}else{
				$(h).parents().find('.status').removeClass('hide');
			}
		}, 1);
	};
	var itemList = <?php echo json_encode($type_items);?>;
    var findItem = (type) => {
        var itemResult;
        $.each(itemList, (index,value) => {
            if(value.type == type) {
                itemResult = value.name;
                return false;
            }
        });
        return itemResult;
    };
	countrow();
	function countrowfist()
    {
        if(!$('table.item-supplier_quotes tbody tr.item').find('input[value=hau]').length)
        {
            createTrItemfist();
        }
    }
    // function countrow()
    // {
    //     var items = $('table.item-purchases tbody').find('tr.item');
    //     console.log(123);
    //     $.each(items, (index,value)=>{
    //     	var type = $(value).find('td:nth-child(1)').find('input#type').val();
    //     	var name_type = '<span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +findItem(type)+ '</span>';
    //         $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);
    //         $(value).find('td:nth-child(6) input.mainQuantity').change();

    //     });
    // }
	$('#type_items').on('change', function(e){
		var type = $('#type_items').val();
		loadItems(type);
	});

    function loadItems(type){
        var custom_item_select=$('#custom_item_select');
        custom_item_select.find('option:gt(0)').remove();
        custom_item_select.selectpicker('refresh');
        if(custom_item_select.length) {
            $.ajax({
                url : admin_url + 'invoice_items/items/' + type,
                dataType : 'json',
            })
            .done(function(data){
                $.each(data, function(key,value){
					custom_item_select.append('<option value="' + value.id + '">' + value.name+'</option>');
                });
                custom_item_select.selectpicker('refresh');
            });
        }
    }
    $(document).on('change', '.custom_item_select', (e)=>{
    	var currentQuantityInput = $(e.currentTarget);

		var id = $(currentQuantityInput).val();
		if(id == '') {
		}
		else {
    		var type = $('option:selected', currentQuantityInput).attr('data-id');
    		var id_order = $('#id_order').val();
			$.post(admin_url + 'supplier_quotes/get_items/'+id+'/'+type,{[csrfData['token_name']] : csrfData['hash']}, function(item){
				var item = JSON.parse(item);
				createTrItem(item,currentQuantityInput,type);
		    });
		}
	});
	var uniqueArray = <?=$i?>;
	var taxes_dropdown_template=<?=json_encode($taxes)?>;
    var createTrItemfist = (item) => {
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?=base_url('assets/images/preview-not-available.jpg')?>">';
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatar" style="text-align: center;">'+name_type+'<input type="hidden" name="items[' + uniqueArray + '][type]" value="hau" /></td>');
        var td2 = $('<td style="min-wight: 100px;"><input type="hidden" class="count" value="'+uniqueArray+'" /><div class="form-group select-placeholder ">\
		             <select class="custom_item_select" id="custom_item_select_'+uniqueArray+'" name="items[' + uniqueArray + '][id_item]" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
		             </select>\
		        </div><div class="color"><div></td>');
        var td3 = $('<td class="hide">???</td>');
        var td4 = $('<td class="hide">???</td>');
        var td5 = $('<td class="hide">???</td>');
        var td7 = $('<td class="hide"><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input mainQuantity" type="text" name="items[' + uniqueArray + '][quantity]" value="1" /></td>');
        var td8 = $('<td ><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input mainQuantity_suppliers" type="text" name="items[' + uniqueArray + '][quantity_suppliers]" value="1" /></td>');
        var td9 = $('<td class="center quantity_warehoue"></td>');
        var td10 = $('<td class="unit_name"></td>');
        var td11 = $('<td class="hide"><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_expected" type="text" name="items[' + uniqueArray + '][price_expected]" value="0" /></td>');
        var td12 = $('<td ><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_suppliers" type="text" name="items[' + uniqueArray + '][price_suppliers]" value="0" /></td>');
        var taxTemplate=taxes_dropdown_template;
        taxTemplate=taxTemplate.replace('name=""','name="items['+uniqueArray+'][tax_id]"');
        var td13 = $('<td>'+taxTemplate+'<input type="hidden" class="tax_rate" name="items['+uniqueArray+'][tax_rate]" value="0"></td>');
        
        var td14 = $('<td class="hide align_right total_expected">0</td>');
        var td15 = $('<td ><input style="width: 100px" onkeyup="formatNumBerKeyUp(this)" class="height_auto H_input align_right promotion_expected" type="text" name="items[' + uniqueArray + '][promotion_expected]" value="0" /></td>');
        var td16 = $('<td class="align_right total_suppliers">0</td>');
        var td17 = $('<td><textarea class="note" name="items['+uniqueArray+'][note]"></textarea></td>');


        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        newTr.append(td12);
        newTr.append(td13);
        newTr.append(td14);
        newTr.append(td15);
        newTr.append(td16);
        newTr.append(td17);
        newTr.append('<td class="delete"></td');
        $('table.item-supplier_quotes tbody').append(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        uniqueArray++;
    }
    var createTrItem = (item,currentQuantityInput,type) => {
        if(typeof(item)=='undefined' || item.length==0) return;
        if( ($('table.item-supplier_quotes tbody tr').find('input[value=' + item.id + ']#product_id').length > 0) && ($('table.item-supplier_quotes tbody tr').find('input[value=' + type + ']#type').length > 0)) {
            alert_float('danger', "Sản phẩm này đã được thêm, vui lòng kiểm tra lại!");
            return;
        }
        var name_type = '<img style="width: 4em;height: 4em;" src="'+item.avatar_1+'"><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +findItem(type)+ '</span>';
        var new_tr = currentQuantityInput.parents('tr');
        var count = new_tr.find('td > input.count').val();
        new_tr.find('td.avatar').html(name_type+'<input type="hidden" id="type" name="items[' + count + '][type]" value="'+type+'" /><input type="hidden" class="id" id="product_id" name="items[' + count + '][id]" value="'+item.id+'" />');
        var unit_name = item.unit_name;
        if(item.unit_name==null){
            unit_name='';
        }
        new_tr.find('.color').html(item.color);
        new_tr.find('td.unit_name').html(unit_name);
        new_tr.find('td.delete').html('<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a>');
        uniqueArray++;
        getTotalPrice();
        countrowfist();
        new_tr.find('td  > input.mainQuantity').change();
    }
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };
    function getTotalPrice()
    {
        var items = $('table.item-supplier_quotes tbody').find('tr.item');
        var total_quantity_expected = 0;
		var total_sub_expected = 0;
		var total_quantity_suppliers = 0;
		var total_sub_suppliers = 0;
        $.each(items, (index,value)=>{
        	if($(value).find('#type').val() != "hau")
        	{
            total_quantity_expected += parseFloat($(value).find('.mainQuantity').val().replace(/\,/g, ''));
            total_sub_expected += parseFloat($(value).find('.total_expected').text().replace(/\,/g, ''));
            total_quantity_suppliers += parseFloat($(value).find('.mainQuantity_suppliers').val().replace(/\,/g, ''));
            total_sub_suppliers += parseFloat($(value).find('.total_suppliers').text().replace(/\,/g, ''));
        	}
        });
        $('.total_quantity_expected').text(formatNumber(total_quantity_expected));
        $('.total_sub_expected').text(formatNumber(total_sub_expected));
        $('.total_quantity_suppliers').text(formatNumber(total_quantity_suppliers));
        $('.total_sub_suppliers').text(formatNumber(total_sub_suppliers));

        var discount_percent_expected = $('#discount_percent_expected').val();
        var valtype_check_expected = $('#valtype_check_expected').val();
        var discount_percent_suppliers = $('#discount_percent_suppliers').val();
        var valtype_check_suppliers = $('#valtype_check_suppliers').val();
        //thống kê dự kiến
        var reduce_cost=unformat_number($('#reduce_cost').val());
        var delivery_cost=unformat_number($('#delivery_cost').val());

        //thống kê NCC
        var discount_percent_suppliers_total = 0;
        var totalAll_suppliers = 0;
        if(valtype_check_suppliers == 1)
        {
        	discount_percent_suppliers_total = total_sub_suppliers * discount_percent_suppliers/100;
        	totalAll_suppliers = total_sub_suppliers - discount_percent_suppliers_total;
        }
        else if(valtype_check_suppliers == 2)
        {
        	discount_percent_suppliers_total = unformat_number(discount_percent_suppliers);
        	if(discount_percent_suppliers_total >= total_sub_suppliers)
        	{
        		$('#discount_percent_suppliers').val(formatNumber(total_sub_suppliers));
        		discount_percent_suppliers_total = total_sub_suppliers;
        	}
        	totalAll_suppliers = total_sub_suppliers - discount_percent_suppliers_total;
        }
        $('.discount_percent_total_suppliers').text(formatNumber(discount_percent_suppliers_total));
        $('.totalPrice_suppliers').text(formatNumber(Number(totalAll_suppliers) + Number(delivery_cost) - Number(reduce_cost)));
    }
    $('#items-form').on('submit', (e)=>{
        if($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', '<?=_l('ch_invalid_value')?>');
        }
    });
    $(document).on('change','select.tax',function(e){
      var tax_id=$(this).val();
      var tax_rate=parseInt($(this).find('option:selected').attr('data-taxrate'));
      var current_row=$(this).parents('tr');
      if(isNaN(tax_rate)) tax_rate=0;
      $(this).parents('tr').find('input.tax_rate').val(tax_rate);
      calculateTotal(e.currentTarget);
    });
    $(document).on('change','.type_check_expected',function(e){
      var val = $(this).val();
      $('#valtype_check_expected').val(val);
      $('#discount_percent_expected').val(0);
    });
    $(document).on('change','.type_check_suppliers',function(e){
      var val = $(this).val();
      $('#valtype_check_suppliers').val(val);
      $('#discount_percent_suppliers').val(0);
    });
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);
        var current_row = currentInput.parents('tr');

        let mainQuantity = unformat_number(current_row.find('.mainQuantity').val());
        let mainQuantity_suppliers = unformat_number(current_row.find('.mainQuantity_suppliers').val());
        let price_expected = unformat_number(current_row.find('.price_expected').val());
        let price_suppliers = unformat_number(current_row.find('.price_suppliers').val());
        let promotion_expected = unformat_number(current_row.find('.promotion_expected').val());
        let tax = current_row.find('.tax_rate').val();

        var total_expected = mainQuantity*price_expected*(1+tax/100);
        var total_suppliers = (mainQuantity_suppliers*price_suppliers*(1+tax/100)) - promotion_expected;
        current_row.find('.total_expected').text(formatNumber(total_expected));
        current_row.find('.total_suppliers').text(formatNumber(total_suppliers));
        getTotalPrice();

    };
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2=x2.substr(0,2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };
    function unformat_number(number)
    {
        var _number=0;
        if(number)
        {
            _number=number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    $(document).on('keyup', '#discount_percent_expected', (e)=>{
        var currentDiscountPercentInput = $(e.currentTarget);
        var valtype_check = $('#valtype_check_expected').val();
        if(valtype_check == 1)
        {
        	if(unformat_number(currentDiscountPercentInput.val()) < 0 )
        	{
        		currentDiscountPercentInput.val(0);
        	}
        	if(unformat_number(currentDiscountPercentInput.val()) > 100 )
        	{
        		currentDiscountPercentInput.val(100);
        	}
        }
        getTotalPrice();
    });
    $(document).on('keyup', '#discount_percent_suppliers', (e)=>{
        var currentDiscountPercentInput = $(e.currentTarget);
        var valtype_check = $('#valtype_check_suppliers').val();
        if(valtype_check == 1)
        {
	        if(unformat_number(currentDiscountPercentInput.val()) < 0 )
	        {
	        	currentDiscountPercentInput.val(0);
	        }
	        if(unformat_number(currentDiscountPercentInput.val()) > 100 )
	        {
	        	currentDiscountPercentInput.val(100);
	        }
        }
        getTotalPrice();
    });
    $(document).on('keyup', '.unit_cost', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.mainQuantity', (e)=>{

        var currentQuantityInput = $(e.currentTarget);
        var quantity_warehoue = currentQuantityInput.parent().parent('tr').find('td select.custom_item_select  option:selected').attr('data-idd');
        quantity_warehoues =  quantity_warehoue - parseInt(unformat_number(currentQuantityInput.val()));
        console.log(quantity_warehoues)
		if(quantity_warehoues < 0)
		{
			currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text(Math.abs(quantity_warehoues));
		}
		if(quantity_warehoues >= 0)
		{
			currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text('');
		}
        calculateTotal(e.currentTarget);
    });
    $(document).on('click', '.mainQuantity', (e)=>{

        var currentQuantityInput = $(e.currentTarget);
        var quantity_warehoue = currentQuantityInput.parent().parent('tr').find('td select.custom_item_select  option:selected').attr('data-idd');
        quantity_warehoues =  quantity_warehoue - parseInt(unformat_number(currentQuantityInput.val()));
        console.log(quantity_warehoues)
		if(quantity_warehoues < 0)
		{
			currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text(Math.abs(quantity_warehoues));
		}
		if(quantity_warehoues >= 0)
		{
			currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text('');
		}
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '.mainQuantity', (e)=>{

        var currentQuantityInput = $(e.currentTarget);
        var quantity_warehoue = currentQuantityInput.parent().parent('tr').find('td select.custom_item_select  option:selected').attr('data-idd');
        quantity_warehoues =  quantity_warehoue - parseInt(unformat_number(currentQuantityInput.val()));
        console.log(quantity_warehoues)
		if(quantity_warehoues < 0)
		{
			currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text(Math.abs(quantity_warehoues));
		}
		if(quantity_warehoues >= 0)
		{
			currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text('');
		}
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.mainQuantity_suppliers', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.price_expected', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.price_suppliers', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.promotion_expected', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('click', '.type_check', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        $('#discount_percent').val(0);
        $('#valtype_check').val(currentQuantityInput.val());
        getTotalPrice();
    });
	countrow();
	getTotalPrice();
    function countrow()
    {
        var items = $('table.item-supplier_quotes tbody').find('tr.item');
        $.each(items, (index,value)=>{
        	var type = $(value).find('td:nth-child(1)').find('input#type').val();
        	var name_type = '<span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +findItem(type)+ '</span>';
            $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);
            $(value).find('td:nth-child(6) input.mainQuantity').change();

        });
    }
        $(document).on('show.bs.dropdown', '.custom_item_select', (e)=>{
           $('.table-responsive').css( "overflow", "inherit" );
        });

        $(document).on('hide.bs.dropdown', '.custom_item_select', (e)=>{
           $('.table-responsive').css( "overflow", "auto" );
        });
	    $(document).on('change','select.tax_all',function(e){
	      var tax_id=$(this).val();
	      var items = $('table.item-purchase_order tbody').find('tr.item');
	        $.each(items, (index,value)=>{
	        	if($(value).find('td:nth-child(1)').find('input#type').val() != 'hau')
	        	{
	            $(value).find('select.tax').selectpicker('val',tax_id);
	            $(value).find('select.tax').change();
		        }
	        });
	    });  
</script>