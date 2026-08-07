<?php init_head(); ?>
<style type="text/css">
  .item-purchase .ui-sortable tr td input {
    width: 80px;
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
									$status = format_supplers_status($items->status, '', false);
								}
								else
									{
									$status = format_supplers_status(-1, '', false);
								}
								?>
				 			<span><?= $status ?></span>
						 </div>
						 <?php 
							if (isset($items))
							{ ?>
						<?php 
							}
						?>
						<h4 class="bold no-margin font-medium">
						     <?php echo _l('ch_supplier_quotes_t'); ?>
						   </h4>
						   <hr />
				 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				 			<div class="panel panel-primary">
								<div class="panel-heading"><?=_l('lead_general_info')?></div>
								<div class="panel-body">
									<?php if(isset($items)) { 
										echo format_quotes_father($items->id);
									?>
									<br>
									<br>
									<?php } ?>
						 			<div class="col-md-4">
									   	<div class="form-group">
							                 <label for="number"><?php echo _l('ch_code_p'); ?></label>
							                 <div class="input-group">
							                  <span class="input-group-addon">
							                    <?php echo (isset($items) ? ($items->prefix) : get_option('prefix_supplier_quotes'));?>-</span>
												<?php 
														$number = sprintf('%06d', ch_getMaxID('id', 'tblsupplier_quotes') + 1);
														$value = (isset($items) ? ($items->code) : $number);
													?>
							                    <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
							                  </div>
						                </div>
						            </div>
						            <div class="col-md-4">
						                <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
			                  			<?php echo render_date_input('date', 'ch_date_p', $value); ?>
			                  		</div>
			                  		<div class="col-md-4">
			                  			<?php 
			                  			$disabled =array();
			                  			if(!empty($items->id_ask_price)){
			                  				$disabled = array('disabled'=>true); 
			                  				echo form_hidden('suppliers_id',$items->suppliers_id);
			                  			}
			                  			?>
			                  			<?php
										$value = (isset($items) ? $items->suppliers_id : '');
										echo render_select('suppliers_id',$suppliers,array('id','company'),'supplier',$value,$disabled);
										?>
									</div>
					               	<div class="col-md-12">
										<?php 
										$value = (isset($items) ? $items->note : "");
										echo render_textarea('note', 'ch_note_t', $value);
										?>
									</div>
					           </div>
					       	</div>
						</div>
						<?php
		                    $customer_custom_fields = false;
		                    if(total_rows(db_prefix().'customfields',array('fieldto'=>'supplier_quotes','active'=>1)) > 0 ){
		                         $customer_custom_fields = true;
		                ?>
			            <?php }?>
			            <?php if($customer_custom_fields) { ?>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				 			<div class="panel panel-info">
								<div class="panel-heading"><?=_l('custom_fields')?></div>
								<div class="panel-body">
					                
				                   	<div role="tabpanel" class="tab-pane" id="custom_fields">
				                    	<?php $value_id = (isset($items) ? $items->id : '');?>
				                      	<?php echo render_custom_fields('supplier_quotes',$value_id); ?>
				                   	</div>
				                   
					           </div>
					       	</div>
						</div>
						<?php } ?>
						<div class="clearfix"></div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							
								<!-- <div class="row">
									<div class="col-md-12" style="padding-top: 35px">
										<a data-toggle="modal" data-target="#myModal"  class="btn btn-success btn-icon" style="float: right;">import excel</a>
									</div>				
								</div> -->
								<div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
								<div class="panel-heading">
									<?= lang('tnh_info_items') ?>
								</div>
								<div class="panel-body">
								<div class="table-responsive" style="min-height: 300px;">
									<!-- <table class="table items item-supplier_quotes no-mtop"> -->
										<table class="dt-tnh table item-supplier_quotes table-bordered table-hover" style="width: 100%;">
										<thead>
											<tr>
												<th><input type="hidden" id="itemID" value="" /><?php echo _l('ch_image'); ?></th>
												<th width="" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('ch_items_name_t'); ?></th>
												<th width="" class="text-left"><?php echo _l('item_unit'); ?></th>
												<th width="" class="text-left"><?php echo _l('item_quantity'); ?></th>
												<th width="" class="text-left"><?php echo _l('price'); ?></th>
												<th width="" class="text-left"><?php echo _l('amount'); ?></th>
												<th width="" class="text-left"><?php echo _l('invoice_table_tax_heading'); ?></th>
												<th width="" class="text-left"><?php echo _l('invoice_total'); ?></th>
												<th></th>
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
                                                    <td class="avatart text-center"><img style="border-radius: 50%;width: 4em;height: 4em;" src="<?=(!empty($value['avatar'])?(file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/'.$value['avatar']) ? base_url('uploads/materials/'.$value['avatar']) : (file_exists('uploads/products/'.$value['avatar']) ? base_url('uploads/products/'.$value['avatar']) : base_url('assets/images/preview-not-available.jpg')))):base_url('assets/images/preview-not-available.jpg'))?>"><br><div id="type_name"></div><input type="hidden" id="type" name="items[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>" />
	                                                    <input type="hidden" id="product_id" name="items[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>">
                                                    </td>
                                                    <td><input type="hidden" id="type"  value="<?php echo $value['type']; ?>" />
							                            <!-- <?php echo render_select('custom_item_select',$itemssss,array('product_id','name_item'),'',$value['product_id']); ?> -->
							                            <select style="width: 250px;" id="custom_item_select_<?=$i?>" class=" custom_item_select" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									                        	<?php foreach($itemssss as $t) { ?>
									                           		<option value="<?php echo $t['product_id']; ?>" <?php echo($value['product_id'] == $t['product_id'] ? 'selected' : '') ?> > <?=$t['name_item']?> </option>
									                           	<?php } ?>
									                    </select>
									                    <br><br><div class="color"></div>
												    </td>
                                                    <td><?=$value['unit']?></td>
                                                    <td><input style="width: 100px" class="height_auto H_input mainQuantity" type="text" name="items[<?php echo $i; ?>][quantity]" onkeyup="formatNumBerKeyUpCus(this)" value="<?=formatNumber($value['quantity'])?>" /></td>
                                                    <td ><input style="width: 100px" class="unit_cost height_auto H_input align_right" type="text" name="items[<?php echo $i; ?>][unit_cost]" onkeyup="formatNumBerKeyUpCus(this)" value="<?=number_format($value['unit_cost'])?>" /></td>
                                                    <td class="align_right" ><?=number_format($total_novat)?></td>
                                                    <td ><?=get_taxes_dropdown_template('items['.$i.'][tax_id]',$value['tax_id'])?>
                                                    <input type="hidden" class="tax_rate" name="items[<?=$i?>][tax_rate]" value="<?=$value['tax_rate']?>"></td>
                                                    <td class="danger align_right"><?=number_format($total)?></td>
                                                    <td><a  href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                                </tr>
                                            <?php 
                                            $i++;
                                            $totalQuantity+=$value['quantity'];
                                        	} }?>
										</tbody>
									</table>
								</div>
								</div>
							</div>
						</div>
				 		</div>
				 	</div>
			 	</div>
				<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
					<div style="width: 85%" class=" pull-left">
						<table class="table tnh-tb noMargin table-color_sum dont-responsive-table" style="table-layout: fixed;">
							<tbody>
								<tr>
									<td style="width: 10%;">
										<span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
									</td>
									<td class="total_quantity_all" style="width: 10%;">
										<?php echo $totalQuantity ?>
									</td>
									<td style="width: 10%;">
										<span class="bold"><?php echo _l('total_price'); ?> :</span>
									</td>
									<td class="total_sub_all" style="width: 10%;">
										0
									</td>
									<td style="width: 10%;">
										<span class="bold"><?php echo _l('ch_tax_all'); ?> :</span>
									</td>
									<td style="width: 10%;">
										<?php $tax_all = (isset($items) ? $items->tax_all : '');?>
										<?=str_replace(' tax',' tax_all',get_taxes_dropdown_template('tax_all',$tax_all))?>

									</td>
									<td style="width: 30%;">
										<div>
											<div style="display: flex; align-items: center; float: left;">
												<span style="float: left;" class="bold"><?php echo _l('Chiết khấu'); ?>:&nbsp;&nbsp;</span>
												<input placeholder="Chiết khấu"  class="form-control" id="discount_percent" value="<?=(isset($items) ? $items->discount_percent : 0)?>" name="discount_percent" style="width: 135px;float: left;" onkeyup="formatNumBerKeyUpCus(this)">
												<div class="clearfix"></div>
											</div>
											<div style="float: left;">
												<input type="text" name="valtype_check" value="<?=((isset($items)) ? $items->valtype_check : 1)?>" class="hide" id="valtype_check">
												<div  style="float: left;"  class="radio radio-primary radio-inline">
													<input type="radio" class="type_check" name="type_check" value="1" <?=(isset($items)?((isset($items)&&$items->valtype_check == 1) ? 'checked' : ''): 'checked')?>>
													<label>%</label>
												</div>
												<br>
												<div  class="radio radio-primary radio-inline">
													<input type="radio"  name="type_check" class="type_check" value="2" <?=((isset($items)&&$items->valtype_check == 2) ? 'checked' : '')?>>
													<label>Tiền</label>
												</div>
											</div>
											<div class="clearfix"></div>
										</div>
									</td>
									<td class="discount_percent_total" style="width: 10%;">
										0
									</td>

									<td style="width: 10%;">
										<span style="color:red" class="bold"><?php echo _l('invoice_total'); ?> :</span>
									</td>
									<td style="color:red; width: 10%;" class="totalPrice">
										0
									</td>
								</tr>
							</tbody>
						</table>
					</div>
		            <?php if(!isset($items)){ ?>
		            <button  style="padding: 20px;"  data-loading-text="Vui lòng đợi!" class="btn btn-info pull-right only-save customer-form-submiter">
		            <?php echo _l( 'submit'); ?>
		            </button>
		            <?php } ?>
		            <?php if(isset($items)&&$items->status == 1){ ?>
		            <button  style="padding: 20px;"  data-loading-text="Vui lòng đợi!" class="btn btn-info pull-right only-save customer-form-submiter">
		            <?php echo _l( 'submit'); ?>
		            </button>
		            <?php } ?>
		            <a href="<?=admin_url('RFQ')?>" style="padding: 20px;margin-right: 10px" class="btn btn-default pull-right">
		            <?php echo _l( 'go_back'); ?>
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
		createTrItemfist();
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
	    var type = $('#type_items').val();
	    if(empty(type))
	    {
	      $('.select_custom_item_select').addClass('hide');
	    }
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
		                    var id_order = $('#id_order').val();
		                    var t = {[csrfData.token_name] : csrfData.hash};
		                    return t.type = e, t.rel_id = "", t.q = "{{{q}}}",t.type_items = type,t.id_order=id_order, void 0 !== a && jQuery.extend(t, a), t
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
		                        type_items: e[i].type_items
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
			    html +='<option data-id='+value.type_items+' value="' + value.value + '">'  + value.text + '</option>';
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

	function upperCaseF(a){
	    setTimeout(function(){
	    	if(!isNaN(a.value))
	    	{
	    		a.value = '';
	    	}
	        a.value = a.value.toUpperCase();
	    }, 1);
	}
	$(function(){
		_validate_form($('#import_items-form'), {
        number: "required",
        number_end: "required",
        type: "required",
        unit: "required",
        quantity: "required",
        cost: "required",
        tax: "required",
    });
	});
	// function import_items(form) {
 //        var data = $(form).serialize();
 //        if (typeof(csrfData) !== 'undefined') {
 //            data[csrfData['token_name']] = csrfData['hash'];
 //        }
 //        var url = form.action;
 //        $.post(url, data,'multipart/form-data').done(function(response) {
 //            response = JSON.parse(response);
 //        })
 //        return false;
 //    }

	$(function(){
		_validate_form($('#supplier_quotes-form'), {
        date: "required",
        number: "required",
        suppliers_id: "required",
    });
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

    function countrowfist()
    {
        if(!$('table.item-purchases tbody tr.item').find('input[value=hau]').length)
        {
            createTrItemfist();
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
			$.post(admin_url + 'import/get_items/'+id+'/'+type,{[csrfData['token_name']] : csrfData['hash']}, function(item){
				var item = JSON.parse(item);
				createTrItem(item,currentQuantityInput,type);
		    });
		}
	});
	var uniqueArray = <?=$i?>;
	var taxes_dropdown_template=<?=json_encode($taxes)?>;
    var createTrItemfist = (item) => {
        var newTr = $('<tr class="sortable item"></tr>');
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?=base_url('assets/images/preview-not-available.jpg')?>">';
        var td1 = $('<td class="avatart text-center">'+name_type+'<input type="hidden" name="items[' + uniqueArray + '][type]" value="hau" /></td>');
        var td2 = $('<td><input type="hidden" class="count" value="'+uniqueArray+'" /><div class="form-group ">\
	             <select class="custom_item_select" style="width:250px;" id="custom_item_select_'+uniqueArray+'" name="items[' + uniqueArray + '][id_item]"  data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
	             <?php echo $html;?>
	             </select>\
	        </div><div class="color"></div></td>');
        var td3 = $('<td class="unit_name"></td>');
        var td4 = $('<td ><input style="width: 100px" onkeyup="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity" type="text" name="items[' + uniqueArray + '][quantity]" value="1" /></td>');
        var td5 = $('<td ><input style="width: 100px" onkeyup="formatNumBerKeyUpCus(this)" class="height_auto H_input align_right unit_cost" type="text" name="items[' + uniqueArray + '][unit_cost]" value="0" /></td>');
        var td6 = $('<td class="align_right">0</td>');
        var taxTemplate=taxes_dropdown_template;

        taxTemplate=taxTemplate.replace('name=""','name="items['+uniqueArray+'][tax_id]"'); 
        var td7 = $('<td>'+taxTemplate+'<input type="hidden" class="tax_rate" name="items['+uniqueArray+'][tax_rate]" value="0"></td>');
        var td8 = $('<td class="align_right">0</td>');
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append('<td class="delete"></td');
        $('table.item-supplier_quotes tbody').append(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        $('#custom_item_select_'+uniqueArray).select2();
        uniqueArray++;
        getTotalPrice();
    }
    var createTrItem = (item,currentQuantityInput,type) => {
    	console.log(item);
        if(typeof(item)=='undefined' || item.length==0) return;
        if( ($('table.item-supplier_quotes tbody tr').find('input[value=' + item.id + ']#product_id').length > 0) && ($('table.item-supplier_quotes tbody tr').find('input[value=' + type + ']#type').length > 0)) {
            alert_float('danger', "Sản phẩm này đã được thêm, vui lòng kiểm tra lại!");
            return;
        }
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;" src="'+item.avatar_1+'"><br><span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #e30000">' +findItem(type)+ '</span>';
        var newTr = currentQuantityInput.parents('tr');
        var count = newTr.find('td > input.count').val();
        newTr.find('td.avatart').html(name_type+'<input type="hidden" id="type" name="items[' + count + '][type]" value="'+type+'" /><input type="hidden" class="id" id="product_id" name="items[' + count + '][id]" value="'+item.id+'" />');
       	var unit_name = item.unit_name;
        if(item.unit_name==null){
            unit_name='';
        }
        newTr.find('td.unit_name').html(unit_name);
        newTr.find('.color').html(item.color);
        newTr.find('td.delete').html('<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a>');
        $('table.item-supplier_quotes tbody').append(newTr);
        $('#custom_item_select').selectpicker('toggle');
        $('#custom_item_select').selectpicker('refresh');
        newTr.find('.selectpicker').selectpicker('refresh');
        var tax_id=$('select.tax_all').val();
        newTr.find('select.tax').selectpicker('val',tax_id);
        newTr.find('select.tax').change();
        uniqueArray++;
        getTotalPrice();
        countrowfist();
    }
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };
    function getTotalPrice()
    {   
        var items = $('table.item-supplier_quotes tbody').find('tr.item');
        var total = 0;
		var totalQuantity = 0;
		var totalnothue =0;
        $.each(items, (index,value)=>{
        	if(!empty($(value).find('#type').val()))
        	{
            totalQuantity += parseFloat($(value).find('td:nth-child(4) > input').val().replace(/\,/g, ''));
            totalnothue += parseFloat($(value).find('td:nth-child(6)').text().replace(/\,/g, ''));
            total += parseFloat($(value).find('td:nth-child(8)').text().replace(/\,/g, ''));
        	}
        });
        var discount_percent = $('#discount_percent').val();
        var valtype_check = $('#valtype_check').val();
        if(valtype_check == 1)
        {
        discount_percent_total = discount_percent*totalnothue/100;
        totalall = total - discount_percent_total
        }
        else
        {
        discount_percent_total = unformat_number(discount_percent);	
        if(discount_percent_total >= totalnothue)
        {
        	$('#discount_percent').val(formatNumber(totalnothue));
        	discount_percent_total = totalnothue
        }
        totalall = total - discount_percent_total
        }
        $('.discount_percent_total').text(formatNumber(discount_percent_total))
        
        $('.total_quantity_all').text(formatNumber(totalQuantity));
        $('.totalPrice').text(formatNumber(totalall));
        $('.total_sub_all').text(formatNumber(total));
    }   
    $('#items-form').on('submit', (e)=>{
        if($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', 'Giá trị không hợp lệ!');    
        } 
    });
   	$(document).on('change','select.tax_all',function(e){
      var tax_id=$(this).val();
      var items = $('table.item-supplier_quotes tbody').find('tr.item');
        $.each(items, (index,value)=>{
        	if(!empty($(value).find('#type').val()))
        	{
            $(value).find('select.tax').selectpicker('val',tax_id);
            $(value).find('select.tax').change();
	        }
        });
    });  
     $(document).on('change','select.tax',function(e){
      var tax_id=$(this).val();
      var tax_rate=parseInt($(this).find('option:selected').attr('data-taxrate'));
      var current_row=$(this).parents('tr');
      if(isNaN(tax_rate)) tax_rate=0;
      $(this).parents('tr').find('input.tax_rate').val(tax_rate);
      calculateTotal(e.currentTarget);
    });
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);
        var current_row=currentInput.parents('tr');

        let quantity = unformat_number(current_row.find('.mainQuantity').val());  
        let price_buy=unformat_number(current_row.find('.unit_cost').val());
        let tax=current_row.find('.tax_rate').val();
        var total=quantity*price_buy*(1+tax/100);
        var totalnothue=quantity*price_buy;
        current_row.find('td:nth-child(6)').text(formatNumber(totalnothue));
        current_row.find('td:nth-child(8)').text(formatNumber(total));
        // refreshTotal();
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
    $(document).on('keyup', '#discount_percent', (e)=>{
        var currentDiscountPercentInput = $(e.currentTarget);
        var valtype_check = $('#valtype_check').val();
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
        	var name_type = '<span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #e30000">' +findItem(type)+ '</span>';
            $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);
            // init_ajax_searchs('items','#custom_item_select_'+index);
            $('#custom_item_select_'+index).select2();

        });
    }   
    $(document).ready(function() {
        $('.table-responsive').on('show.bs.dropdown', function () {
           $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
           $('.table-responsive').css( "overflow", "auto" );
        })
    });
</script>