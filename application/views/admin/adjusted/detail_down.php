<?php init_head(); ?>
<style type="text/css">
  .item-items .ui-sortable tr td input {
    width: 80px;
  }
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(), array('id' => 'adjusted-form', 'class' => '_transaction_form invoice-form'));
			if (isset($invoice)) {
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
									$status = format_import_status($items->status, '', false);
								}
								else
								{
									$status = format_import_status(-1, '', false);
								}
							?>
				 			<span><?= $status ?></span>
						 </div>
						 <?php 
						 	$disabled = array();
						 	$readonly = array();
						 if (isset($items)) { 
						 	$disabled = array('disabled'=>true);
						 	$readonly = array('readonly'=>true);
						} ?>
						<h4 class="bold no-margin font-medium">
					     	<?php echo $title; ?>
					   	</h4>
						<hr />
						<input type="text" name="type" value="2" class="hide">
				 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				 			<div class="alert alert-warning text-center total_debt hide"></div>
				 			<div class="panel panel-primary">
								<div class="panel-heading"><?=_l('lead_general_info')?></div>
								<div class="panel-body">
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
										                    <?php echo (isset($items) ? ($items->prefix) : get_option('prefix_detail_down'));?></span>
															<?php 
																	$number = sprintf('%06d', ch_getMaxID('id', 'tbladjusted') + 1);
																	$value = (isset($items) ? ($items->code) : $number);
																?>
										                    <input type="text" name="code" class="form-control" value="<?=$value ?>" readonly>
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
			                  						<?php echo render_date_input('date', '', $value,$readonly); ?>
												</td>
											</tr>
											<tr>
												<td>
													<label for="suppliers_id" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('warehouse'); ?>
													</label>
												</td>
												<td>
													<?php
														$value = (isset($items) ? $items->warehouse_id : '');
														echo render_select('warehouse_id',$warehouse,array('id','name','code'),'',$value,$disabled);
													?>
													<input type="hidden" id="warehouse_idd" name="warehouse_idd" value="" />
												</td>
												<td>
													<label for="type_items" class="control-label">
														<?php echo _l('ch_type'); ?>
													</label>
												</td>
												<td>
													<?php
											         	echo render_select('type_items', $type_items, array('type', 'name'),'',-1);
													?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 mbot50">
							<div class="panel panel-warning">
								<div class="panel-heading"><?=_l('ch_items_down')?></div>
								<div class="panel-body">
									<table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;width: 100%;">
										<tbody>
											<tr>
												<td style="width: 30%;">
													<label for="number" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('tnh_items'); ?>
													</label>
												</td>
												<td style="width: 70%;">
								                    <input data-placeholder="<?=_l('Danh sách mặt hàng')?>" id="custom_item_select" class="custom_item_select" style="width: 100%" >
												</td>
											</tr>
											<tr>
												<td>
													<label for="number" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('tbllocaltion_warehouses'); ?>
													</label>
												</td>
												<td>
					                  				<select style="width: 100%;" id="localtion_id" class="localtion_id"  data-live-search="true" data-width="100%" data-placeholder="<?=_l('Danh sách vị trí')?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										            </select>
												</td>
											</tr>
											<tr>
												<td>
													<label for="reason" class="control-label">
														<?php echo _l('ch_note_t'); ?>
													</label>
												</td>
												<td colspan="3">
													<?php $value = (isset($items) ? $items->note : ""); ?>
													<?php echo render_textarea('note', '', $value); ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 ">
							<div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
								<div class="panel-heading">
									<?= lang('tnh_info_items') ?>
								</div>
								<div class="panel-body">
									<h4 class="mtop0 hide note_quantity_time" style="color: red"><?=_l('note_quantity_time')?></h4>
								<div class="table-responsive" style="max-height: 310px;">
									<table class="dt-tnh table item-adjusted table-bordered table-hover mtop0 mbot0" style="width: 100%;table-layout: fixed;">
									<!-- <table class="table items item-adjusted no-mtop dont-responsive-table"> -->
										<thead>
											<tr>
												<th style="width: 100px"></th>
												<th style="width: 300px" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
												<th style="width: 100px" class="text-center"><?php echo _l('item_unit'); ?></th>
												<th style="width: 100px" class="text-center"><?php echo _l('cong_price_thinh'); ?></th>
												<th style="width: 100px" class="text-center quantity_time hide"><?php echo _l('ch_quantity_ht'); ?></th>
												<th style="width: 100px" class="text-center"><?php echo _l('item_quantity'); ?></th>
                                                <th style="width: 120px" class="text-center"><?php echo _l('ch_quantity_down'); ?></th>
                                                <th style="width: 100px" class="text-center"><?php echo _l('ch_quantity_time'); ?></th>
                                                <th style="width: 100px" class="text-center check_quantity_time hide"><?php echo _l('ch_ap_new'); ?></th>
                                                <th style="width: 100px" class="text-center"><?php echo _l('amount_suppliers_vnd'); ?></th>
												<th style="width: 50px"><i class="fa fa-trash-o" aria-hidden="true"></i></th>
											</tr>
										</thead>
										<tbody>
										<?php
                                            $i=0;
                                            $totalQuantity_approve=0;
                                            $totalQuantity=0;
                                            if(isset($items) && count($items->items) > 0) {
                                                foreach($items->items as $value) {
                                                    ?>
                                                <tr class="sortable item">
                                                    <td class="text-center"><input class="hide idd" id="idd" name="items[<?php echo $i; ?>][idd]" value="<?php echo $value['id']; ?>" /><input class="hide type"  id="type" name="items[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>" /><input class="hide id"  id="id" name="items[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>" /><input class="hide localtion" id="localtion" name="items[<?php echo $i; ?>][localtion]" value="<?php echo $value['localtion']; ?>" /><img style="border-radius: 50%;width: 2em;height: 2em;" src="<?=(!empty($value['avatar'])?(file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/'.$value['avatar']) ? base_url('uploads/materials/'.$value['avatar']) : (file_exists('uploads/products/'.$value['avatar']) ? base_url('uploads/products/'.$value['avatar']) : base_url('assets/images/preview-not-available.jpg')))):base_url('assets/images/preview-not-available.jpg'))?>"><br><?=format_item_purchases($value['type'])?>
												    </td>
												    <td class="dragger">
												    	<?=$value['name_item']?><br><?=$value['localtion_name_id']?>

												    </td>
												    <td><?=$value['unit']?></td>
												    <td>
												    	<input  class="mainQuantity H_input height_auto" type="text" readonly name="items[<?=$i?>][quantity]" value="<?=number_format($value['quantity'])?>" />
												    </td>
												    <td>
												    	<input class="mainQuantityNet H_input height_auto"  type="text" name="items[<?=$i?>][quantity_net]" value="<?=number_format($value['quantity_net'])?>" />
												    </td>
												    <td>
												    	<input class="mainQuantityDiff H_input height_auto"  type="text" readonly name="items[<?=$i?>][quantity_diff]" value="<?=number_format($value['quantity_diff'])?>" />
												    </td>
        											<td><?=$value['handling']?><input class="handling" type="hidden" name="items[<?=$i?>][handling]" value="<?=$value['handling']?>"></td>
        											<td>
        												<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;">
        													<i class="fa fa-times"></i>
        												</a>
        											</td>
                                                </tr>
                                            <?php 
                                            $i++;
                                        	} }?>
										</tbody>
									</table>
								</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
				 		</div>
				 	</div>
			 	</div>
				<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
					<div class="pull-left" style="width: 80% !important;">
						<table class="table tnh-tb noMargin table-color_sum dont-responsive-table">
							<tbody>
								<tr>
									<td>
										<span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
									</td>
									<td class="total_all">
										<?php echo $totalQuantity ?>
									</td>
									<td>
										<span class="bold"><?php echo _l('ch_quantity_down_total'); ?> :</span>
									</td>
									<td class="total_net">
										<?php echo $totalQuantity_approve ?>
									</td>
									<td>
										<span class="bold"><?php echo _l('ch_quantity_time_total'); ?> :</span>
									</td>
									<td class="total_diff">
										<?php echo $totalQuantity_approve ?>
									</td>
									<td>
										<span class="bold"><?php echo _l('cong_info_money'); ?> :</span>
									</td>
									<td class="total_amount">
										<?php echo $totalQuantity_approve ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a style="margin-left: 10px;" class="hide btn btn-info pull-right form-submiters_all">
		            <?php echo _l('Áp dụng tất cả và lưu');?>
		            </a>
		            <a class="btn btn-info pull-right form-submitersss">
		            <?php echo _l('submit'); ?>
		            </a>
		        </div>
			</div>
			
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
		$(function(){
			var dt = $('.item-purchases').DataTable({
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
		$(function(){
			$('#date').attr('disabled', true);
			_validate_form($('#adjusted-form'), {
	        date: "required",
	        number: "required",
	        warehouse_id: "required",
	    	});
		});
		$(document).on('change', '#warehouse_id', (e)=>{

        loadLocaltion_warehouses();
        var warehouse=$('#warehouse_id').val();
        $('#warehouse_idd').val(warehouse);
		})
		$('#warehouse_id').change();
		refreshQuantity();
		function ajaxSelectCallBack(element, url, id, types = '')
            {
                if (id > 0)
                {
                    $(element).val(id).select2({
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: true,
                        initSelection: function (element, callback) {
                            $.ajax({
                                type: "get", async: false,
                                url: url + '/' + id,
                                dataType: "json",
                                success: function (data) {
                                    callback(data.results[0]);
                                }
                            });
                        },
                        ajax: {
                            url: url,
                            dataType: 'json',
                            quietMillis: 15,
                            data: function (term, page) {
                                return {
                                    type:$('#type_items').val(),
                                    types: types,
                                    term: term,
                                    limit: 50
                                };
                            },
                            results: function (data, page) {
                                if (data.results != null) {
                                    return {results: data.results};
                                } else {
                                    return {results: [{id: '', text: 'No Match Found'}]};
                                }
                            }
                        },
                            formatResult: repoFormatSelection,
                            formatSelection: repoFormatSelection,
                            dropdownCssClass: "bigdrop",
                            escapeMarkup: function (m) { return m; }
                    });
                } else {
                    $(element).select2({
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: true,
                        ajax: {
                            url: url + '/' + $(element).val(),
                            dataType: 'json',
                            quietMillis: 15,
                            data: function (term, page) {
                                return {
                                    type:$('#type_items').val(),
                                    types: types,
                                    term: term,
                                    limit: 50
                                };
                            },
                            results: function (data, page) {
                                if(data.results != null) {
                                    return { results: data.results };
                                } else {
                                    return { results: [{code_client:'',id: '', text: 'No Match Found'}]};
                                }
                            }
                        },
                        formatResult: repoFormatSelection,
                        formatSelection: repoFormatSelection,
                        dropdownCssClass: "bigdrop",
                        escapeMarkup: function (m) { return m; }
                    });
                }
            }
                var base_url = '<?=base_url()?>';
    function repoFormatSelection(state) {
        if (!state.id) return state.text;
        if(state.img)
        {
            var img = '<img class="img_option" src="'+base_url+state.img+'"/> ';
        }
        else
        {
            var img = '<img class="img_option" src="'+base_url+'download/preview_image"/> ';
        }
        return  img + state.text + ' - '+ C_formatNumber(state.price);
    }
           	ajaxSelectCallBack($('.custom_item_select'), "<?=admin_url('transfer/SearchItems')?>", 0);
           	$('#localtion_id').select2();
		    function loadLocaltion_warehouses(){
		       	var warehouse=$('#warehouse_id').val();
		       	$('#localtion_id').select2();
		       	$('#localtion_id').find('option:gt(0)').remove();
		       	if($('#localtion_id').length) {
		       		$.post(admin_url+"warehouse/list_localtion",{warehouse:warehouse,[csrfData['token_name']] : csrfData['hash']},function(data){
		       		$('#localtion_id').html(data).find('option').attr('disabled','disabled').parents('#localtion_id').find('option[child="1"]').removeAttr('disabled').selectpicker('render');
		            $('#localtion_id').find('option:nth-child(1)').removeAttr('disabled');
		        	})
		       	}
		       	$('#custom_item_select').select2('val','');
		   	}
		   	$('#custom_item_select').change((e)=>{
		   		if(!$('div #warehouse_id option:selected').length || $('div #warehouse_id option:selected').val() == '') {
			            alert_float('danger', "<?=_l('ch_not_warehouse')?>");
			            return;
			        }
		        var id = $(e.currentTarget).val();
		        var warehouses = $('#warehouse_id').val();
		        var date =$('#date').val();
		        var type = $(e.currentTarget).select2('data').type;
		        if(typeof(id) != 'undefined') { 
		            dataString1={type:type,id:id,warehouses:warehouses,date:date,[csrfData['token_name']] : csrfData['hash']};
		            jQuery.ajax({
		              type: "post",
		              url:"<?=admin_url()?>warehouse/get_localtion/",
		              data: dataString1,
		              cache: false,
		              success: function (data) {
		                data = JSON.parse(data);
		                if(!empty(data))
		                {
		                    $.each(data, function(key,value){
		                        createTrItem(value,type);
		            		});
		                }
		            }
		        });
		        }
		        else 
		        {   
		            alert('<?=_l('ch_not_itemss_chose')?>');
		            isNew = false;
		            $('#btnAdd').hide();
		        }
		    });
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
		    var uniqueArray  = <?=$i?>;
		    var total = 0;
		   	function createTrItem(data,type)
			    {
			        if(!$('div #warehouse_id option:selected').length || $('div #warehouse_id option:selected').val() == '') {
			            alert_float('danger', "<?=_l('ch_not_warehouse')?>");
			            return;
			        }
			        if($('table.item-adjusted tbody tr').find('input[value=' + data.items.id + '].id ').length > 0) {
			        	var parents = $('table.item-adjusted tbody tr').find('input[value=' + data.items.id + '].id ').parents('tr');
			        	if((parents.find('input[value=' + type + '].type ').length > 0)&&(parents.find('input[value=' + data.localtion + '].localtion ').length > 0))
			        	{
			        	alert_float('warning', "<?=_l('ch_exsit_items_rfq')?>");
			            return;	
			        	}
			        }
			        var newTr = $('<tr class="sortable item"></tr>');
			        
			        var td1 = $('<td class="text-center"><img style="border-radius: 50%;width: 2em;height: 2em;" src="'+data.items.avatar_1+'"><br><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +findItem(type)+ '</span><input class="hide id"  name="items[' + uniqueArray + '][id]" value="'+data.items.id+'" />'+
			            '<input class="hide localtion" name="items[' + uniqueArray + '][localtion]" value="'+data.localtion+'" /><input class="hide type" name="items[' + uniqueArray + '][type]" value="'+type+'" /></td>');
			        var td2 = $('<td class="dragger">'+data.items.name+'<br>'+data.name_localtion+'</td>');
			        var td3 = $('<td>'+data.items.unit_name+'</td>');
			        var td9 = $('<td><input onkeyup="formatNumBerKeyUp(this)" style="width:100%" class="height_auto price H_input align_right" type="text" name="items[' + uniqueArray + '][price]" value="'+formatNumber(data.items.price_import)+'" /></td>');
			        var td6 = $('<td class="quantitynet_time text-center hide">0</td>');
			        var td4 = $('<td><input readonly style="width:100%" class="height_auto mainQuantity H_input" type="text" name="items[' + uniqueArray + '][quantity]" value="'+data.get_quantity_import+'" /></td>');  
			        var td5 = $('<td><input style="width:100%" onkeyup="formatNumBerKeyUpCus(this)" class="height_auto mainQuantityNet H_input" type="text" name="items[' + uniqueArray + '][quantity_net]" value="" /></td>');   
			        
			        var td7 = $('<td class="text-center"><input class="handling" type="hidden" name="items[' + uniqueArray + '][handling]" value=""></td>');
			        var td8 = $('<td class="check_quantitynet_time hide"><a href="#" class="button_check btn btn-info pull-right" onclick="check_quantitynet_time(this); return false;" ><i class="fa fa-check"></i></a><input  type="text"  class="hide" name="items[' + uniqueArray + '][check]" value="0" /></td>');	
			        var td10 = $('<td class="amount text-right">0</td>'); 
			        newTr.append(td1);
			        newTr.append(td2);
			        newTr.append(td3);
			        newTr.append(td9);
			        newTr.append(td6);
			        newTr.append(td4);
			        newTr.append(td5);
			        newTr.append(td7);
			        newTr.append(td8);
			        newTr.append(td10);
			        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
			        $('table.item-adjusted tbody').prepend(newTr);

			        total+=parseFloat($('tr.main').find('td:nth-child(4) > input').val());
			        uniqueArray++;
			        // $('#custom_item_select').val('').selectpicker('render'); 
			        refreshQuantity();
			    };
				var deleteTrItem = (trItem) => {
					var r = confirm("<?php echo _l('confirm_action_prompt');?>");
				    if (r == false) {
				        return false;
				    } else {
				        var current = $(trItem).parent().parent();
				        $(trItem).parent().parent().remove();
				        refreshQuantity();
			    	}
			    };
			    $(document).on('keyup', '.price', (e)=>{
			        var currentQuantityInput = $(e.currentTarget).parents('tr').find('input.mainQuantityNet');
			        quantity=$(e.currentTarget).parents('tr').find('input.mainQuantity');
			        quantityDiff=$(e.currentTarget).parents('tr').find('input.mainQuantityDiff');
			        handlingInput=$(e.currentTarget).parents('tr').find('input.handling');
			        handlingTd=handlingInput.parent();
			        var diff=(Number(quantity.val()) - Number(currentQuantityInput.val()));
			        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
					$(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total*diff)));
			        refreshQuantity();
			    });
			    $(document).on('change', '.price', (e)=>{
			        var currentQuantityInput = $(e.currentTarget).parents('tr').find('input.mainQuantityNet');
			        quantity=$(e.currentTarget).parents('tr').find('input.mainQuantity');
			        quantityDiff=$(e.currentTarget).parents('tr').find('input.mainQuantityDiff');
			        handlingInput=$(e.currentTarget).parents('tr').find('input.handling');
			        handlingTd=handlingInput.parent();
			        var diff=(Number(quantity.val()) - Number(currentQuantityInput.val()));
			        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
					$(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total*diff)));
			        refreshQuantity();
			    });
			    $(document).on('keyup', '.mainQuantityNet', (e)=>{
			        var currentQuantityInput = $(e.currentTarget);
			        var mainQuantityNet = currentQuantityInput.val();
			        if(currentQuantityInput.val() < 0)
			        {
			        	alert('<?=_l('ch_quantity_l0')?>!');
			        	currentQuantityInput.val(0);
			        	mainQuantityNet = 0;
			        }
			        quantity=$(e.currentTarget).parents('tr').find('input.mainQuantity');
			        handlingInput=$(e.currentTarget).parents('tr').find('input.handling');
			        handlingTd=handlingInput.parent();
			        var handling=tnhFormatNumber(Number(quantity.val()) - Number(mainQuantityNet));
			        if(handling < 0)
			        {
			        	var r = confirm("<?php echo _l('ch_quantity_am');?>");
					    if (r == false) {
					    	currentQuantityInput.val(0);
					        return false;
					    } else {

					    }
			        }
			        handlingTd.text(handling);
			        handlingInput.val(handling);
			        handlingTd.append(handlingInput);
			        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
					$(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total*(Number(quantity.val()) - Number(mainQuantityNet)))));
			        // quantityDiff.val(Number(currentQuantityInput.val())+Number(quantity.val()));
			        refreshQuantity();
			    });
			    $(document).on('change', '.mainQuantityNet', (e)=>{
			        var currentQuantityInput = $(e.currentTarget);
			        var mainQuantityNet = currentQuantityInput.val();
			        if(currentQuantityInput.val() < 0)
			        {
			        	alert('<?=_l('ch_quantity_l0')?>!');
			        	currentQuantityInput.val(0);
			        	mainQuantityNet = 0;
			        }
			        quantity=$(e.currentTarget).parents('tr').find('input.mainQuantity');
			        handlingInput=$(e.currentTarget).parents('tr').find('input.handling');
			        handlingTd=handlingInput.parent();
			        var handling=tnhFormatNumber(Number(quantity.val()) - Number(mainQuantityNet));
			        handlingTd.text(handling);
			        handlingInput.val(handling);
			        handlingTd.append(handlingInput);
			        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
					$(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total*(Number(quantity.val()) - Number(mainQuantityNet)))));
			        // quantityDiff.val(Number(currentQuantityInput.val())+Number(quantity.val()));
			        refreshQuantity();
			    });
			    $(document).on('click', '.mainQuantityNet', (e)=>{
			        var currentQuantityInput = $(e.currentTarget);
			        var mainQuantityNet = currentQuantityInput.val();
			        if(currentQuantityInput.val() < 0)
			        {
			        	alert('<?=_l('ch_quantity_l0')?>!');
			        	currentQuantityInput.val(0);
			        	mainQuantityNet = 0;
			        }
			        quantity=$(e.currentTarget).parents('tr').find('input.mainQuantity');
			        handlingInput=$(e.currentTarget).parents('tr').find('input.handling');
			        handlingTd=handlingInput.parent();
			        var handling=tnhFormatNumber(Number(quantity.val()) - Number(mainQuantityNet));
			        if(handling < 0)
			        {
			        	var r = confirm("<?php echo _l('ch_quantity_am');?>");
					    if (r == false) {
					    	currentQuantityInput.val(0);
					        return false;
					    } else {

					    }
			        }	
			        handlingTd.text(handling);
			        handlingInput.val(handling);
			        handlingTd.append(handlingInput);
			        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
					$(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total*(Number(quantity.val()) - Number(mainQuantityNet)))));
			        // quantityDiff.val(Number(currentQuantityInput.val())+Number(quantity.val()));
			        refreshQuantity();
			    });
			    function refreshQuantity(){
			        var items = $('table.item-adjusted tbody tr');
			        total = 0;
			        total_net = 0;
			        total_diff = 0;
			        total_amount = 0;
			        $.each(items, (index,value)=>{
			            let temp=parseFloat($(value).find('input.mainQuantity').val());
			            total += (isNaN(temp)?0:temp);
			            temp=parseFloat($(value).find('input.mainQuantityNet').val());
			            total_net += (isNaN(temp)?0:temp);
			            temp=parseFloat($(value).find('input.handling').val());
			            total_diff += (isNaN(temp)?0:temp);
			            temp=parseFloat(unformat_number($(value).find('td.amount').text()));
			            total_amount += (isNaN(temp)?0:temp);
			        });
			        $('.total_all').text(formatNumber(total));
			        $('.total_net').text(formatNumber(total_net));
			        $('.total_diff').text(formatNumber(total_diff));
			        $('.total_amount').text(formatNumber(total_amount));
			        if(items.length > 0)
			        {
			        	
			        	$('#warehouse_id').prop('disabled', 'disabled');
			        }else
			        {
			        	
			        	$('#warehouse_id').prop('disabled', false);
			        }
			    };
			    $('#localtion_id').change((e)=>{
			        var localtion = $(e.currentTarget).val();
			        var warehouses = $('#warehouse_id').val();
			        var date =$('#date').val();
			        var id=$('#custom_item_select').val();
			        if(empty(id))
			        {
			        	alert('<?=_l('ch_not_itemss_chose')?>');
			        	$(e.currentTarget).select2('val','');
			        	return;
			        }
			        var type = $('#custom_item_select').select2('data').type;
		            dataString={type:type,id:id,warehouses:warehouses,date:date,localtion:localtion,[csrfData['token_name']] : csrfData['hash']};
		            jQuery.ajax({
		              type: "post",
		              url:"<?=admin_url()?>warehouse/get_localtion/",
		              data: dataString,
		              cache: false,
		              success: function (data) {
		              	data = JSON.parse(data);
		              	$.each(data, function(key,value){
		                        createTrItem(value,type);
		            	});
		            	$(e.currentTarget).select2('val','');
		            }
		        });
			    });
		$('.form-submitersss').on('click', (e)=>{
     	var product_id ='';
     	var test_quantity = 0;
        var items = $('table.item-adjusted tbody tr');
        if(items.length == 0)
        {
            alert_float('danger', "<?=_l('ch_not_itemss')?>");
            return;
    	}
        $.each(items, (index,value)=>{
        	var type = $(value).find('td:nth-child(1)').find('input.id').val()+'|'+$(value).find('td:nth-child(1)').find('input.localtion').val()+'|'+$(value).find('td:nth-child(1)').find('input.type').val()+'|'+$(value).find('td:nth-child(6)').find('input').val();
        	product_id+=type+',';
        });
        var warehouse_id = $('#warehouse_id').val();
        dataString = {warehouse_id:warehouse_id,product_id: product_id,[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>adjusted/test_quantity_sumbit/",
            data: dataString,
            cache: false,
            success: function (data) {
            	data = JSON.parse(data);
		        if(data.test_quantity > 0)
		        {

		        	alert("<?=_l('ch_quantity_tiem')?>");
		        	$('.quantity_time').removeClass('hide');
		        	$('.check_quantity_time').removeClass('hide');
	        		$('.form-submiters_all').removeClass('hide');
	        		$('.note_quantity_time').removeClass('hide');
		        	$.each(items, (index,value)=>{
	        				$(value).find('td:nth-child(5)').removeClass('hide');
	        				$(value).find('td:nth-child(9)').removeClass('hide');

	        				var quantity = $(value).find('td:nth-child(6)').find('input').val();
	        				$(value).find('td:nth-child(9)').removeClass('hide');
	        				if(quantity != data.items[index].quantity)
	        				{
	        				$(value).find('td:nth-child(9)').find('input').val(1);
	        				$(value).find('td:nth-child(9)').find('.button_check').removeClass('hide');
	        				}
			        		$(value).find('td:nth-child(5)').html(data.items[index].quantity);
			        });
		        	return;
		        } else {
		        	if($('input.error').length) {
			            e.preventDefault();
			            alert('<?=_l('ch_invalid_value')?>'); 
			            return;
		        	}
		        	var a=confirm("<?=_l('ch_you_want_update')?>");
		        	if(a===false)
		        	{
		            	e.preventDefault();    
		        	} else {
		        		$('#adjusted-form').submit();
		        	} 
		    	}
            }
        });
    });
	$('.form-submiters_all').on('click', (e)=>{

     	var product_id ='';
     	var test_quantity = 0;
        var items = $('table.item-adjusted tbody tr');
        if(items.length == 0)
        {
            alert_float('danger', "<?=_l('ch_not_itemss')?>");
            return;
    	}
        $.each(items, (index,value)=>{
        	if($(value).find('td:nth-child(9)').find('input').val() == 1)
        	{
        		$(value).find('td:nth-child(9)').find('.button_check').click();
        	}
        	var type = $(value).find('td:nth-child(1)').find('input.id').val()+'|'+$(value).find('td:nth-child(1)').find('input.localtion').val()+'|'+$(value).find('td:nth-child(1)').find('input.type').val()+'|'+$(value).find('td:nth-child(6)').find('input').val();
        	product_id+=type+',';
        });
        var warehouse_id = $('#warehouse_id').val();
        dataString = {warehouse_id:warehouse_id,product_id: product_id,[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>adjusted/test_quantity_sumbit/",
            data: dataString,
            cache: false,
            success: function (data) {
            	data = JSON.parse(data);
		        if(data.test_quantity > 0)
		        {

		        	alert("<?=_l('ch_quantity_tiem')?>");
		        	$('.quantity_time').removeClass('hide');
		        	$('.note_quantity_time').removeClass('hide');
		        	$('.check_quantity_time').removeClass('hide');
		        	$.each(items, (index,value)=>{
	        				$(value).find('td:nth-child(5)').removeClass('hide');
	        				$(value).find('td:nth-child(9)').removeClass('hide');

	        				var quantity = $(value).find('td:nth-child(6)').find('input').val();
	        				$(value).find('td:nth-child(9)').removeClass('hide');
	        				if(quantity != data.items[index].quantity)
	        				{
	        				$(value).find('td:nth-child(9)').find('input').val(1);
	        				$(value).find('td:nth-child(9)').find('.button_check').removeClass('hide');
	        				}
			        		$(value).find('td:nth-child(5)').html(data.items[index].quantity);
			        });
		        	return;
		        } else {
		        	if($('input.error').length) {
			            e.preventDefault();
			            alert('<?=_l('ch_invalid_value')?>'); 
			            return;
		        	}
		        	var a=confirm("<?=_l('ch_you_want_update')?>");
		        	if(a===false)
		        	{
		            	e.preventDefault();    
		        	} else {
		        		$('#adjusted-form').submit();
		        	} 
		    	}
            }
        });
    });

	var check_quantitynet_time = (trItem) => {
        var current = $(trItem).parent().parent();
        var quantity_time = current.find('td:nth-child(4)').text();
        current.find('td:nth-child(5)').find('input').val(quantity_time);
        current.find('td:nth-child(6)').find('input').change();
        current.find('td:nth-child(8)').find('input').val(0);
        current.find('td:nth-child(8)').find('.button_check').addClass('hide');
        // $(trItem).parent().parent().remove();
        // refreshQuantity();
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
</script>