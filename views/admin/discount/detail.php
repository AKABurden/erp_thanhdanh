<?php init_head(); ?>
<style type="text/css">
  .item-discount .ui-sortable tr td input {
    width: 80px;
  }
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(), array('id' => 'discount-form', 'class' => '_transaction_form invoice-form'));
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
						if (!isset($discount))
							$type = 'warning';
						elseif ($discount->status == 0)
							$type = 'warning';
						elseif ($discount->status == 1)
							$type = 'info';
						elseif ($discount->status == 2)
							$type = 'success';

						?>
				 	
						 <?php
							if (isset($discount))
							{ ?>
						<?php
							}
						?>
						<h4 class="bold no-margin font-medium">
						     <?php echo _l('Thông tin chiết khấu thương mại'); ?>
						   </h4>
						   <hr style="margin-top: 0px;margin-bottom: 0px;" />
						   <br>
				 		<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
				 			<div class="panel panel-primary">
								<div class="panel-heading"><?=_l('lead_general_info')?></div>
								<div class="panel-body">
									<table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
										<tbody>
											<tr>
												<td style="width: 30%">
													<label for="number" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('ch_code_p'); ?>
													</label>
												</td>
												<td style="width: 70%">
													<div class="form-group">
										                <div class="input-group">
										                  	<span class="input-group-addon">
										                    <?php echo (isset($discount) ? ($discount->prefix) : get_option('prefix_discount'));?></span>
															<?php
																	$number = sprintf('%06d', ch_getMaxID('id', 'tbldiscount') + 1);
																	$value = (isset($discount) ? ($discount->code) : $number);
																?>
										                    <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
										                </div>
									                </div>
												</td>
											</tr>
											<tr>
												<td>
													<label for="date" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('Tên bảng chiết khấu'); ?>
													</label>
												</td>
												<td>
													<?php $name_discount = (isset($discount) ? $discount->name_discount : ''); ?>
				                  					<?php echo render_input('name_discount','',$name_discount); ?>
												</td>
											</tr>
											<tr>
												<td>
													<label for="date" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('ch_date_p'); ?>
													</label>
												</td>
												<td>
													<?php $value = (isset($discount) ? _d($discount->date) : _d(date('Y-m-d'))); ?>
				                  					<?php echo render_date_input('date', '', $value); ?>
												</td>
											</tr>
											<tr>
												<td>
													<label for="date" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('Ngày hiệu lực'); ?>
													</label>
												</td>
												<td>
													<div class="form-group">
														<?php $value = (isset($discount) ? _d($discount->date_start).' - '._d($discount->date_end) : ''); ?>
										               <div class="input-group">
										                  <input type="text" id="effective_date" name="effective_date" value="<?=$value?>" class="form-control effective_date" aria-invalid="false">
										                  <div class="input-group-addon">
										                     <i class="fa fa-calendar calendar-icon"></i>
										                  </div>
										               </div>
										            </div>
												</td>
											</tr>
											<tr>
												<td>
													<label for="name" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('Nhóm khách hàng'); ?>
													</label>
												</td>
												<td>
													<?php $value = (isset($discount) ? explode(',', $discount->type_client)  : ''); ?>
				                  					<?php
										                echo render_select('type_client[]',$market,array('id','name'),'',$value,array('data-actions-box'=>1,'multiple'=>true,'required'=>true));
										            ?>
												</td>
											</tr>
											<tr>
												<td>
													<label for="type_items" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('Khách hàng'); ?>
													</label>
												</td>
												<td>
													<div class="form-group">
														<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 radio radio-primary radio-inline">
												        <input type='radio' <?=(isset($discount) ? ($discount->id_client == 2 ? ''  : 'checked') : 'checked'); ?> style="width: 60px;" id='radio_1'  name='id_client' value='1' />&nbsp;&nbsp;<label>    Tất cả</label>
													    </div>
													    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 radio radio-primary radio-inline" style="margin-left:0;">
												        <input <?=(isset($discount) ? ($discount->id_client == 2 ? 'checked'  : '') : ''); ?> type='radio' style="width: 60px;" id='radio_2' name='id_client' value='2' />&nbsp;&nbsp;<label>   Tùy chọn</label>
													    </div>
												    </div>
												</td>
											</tr>
											<tr>
												<td>
													<label for="reason" class="control-label">
														<?php echo _l('ch_note_t'); ?>
													</label>
												</td>
												<td >
													<textarea rows="3" id="note" name="note" class="form-control " value=""><?=(isset($discount) ? $discount->note : "");?></textarea>
												</td>
											</tr>
											<tr>
												<td>
													<label for="type_items" class="control-label">
														<small class="req text-danger">* </small>
														<?php echo _l('Trạng thái'); ?>
													</label>
												</td>
												<td>
													<div class="form-group">
														<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 radio radio-primary radio-inline">
												        <input <?=(isset($discount) ? ($discount->apply == 2 ? ''  : 'checked') : 'checked'); ?> type='radio' style="width: 60px;" id='radio_1' checked name='apply' value='1' />&nbsp;&nbsp;<label><?=_l('active')?></label>
													    </div>
													    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 radio radio-primary radio-inline" style="margin-left:0;">
												        <input <?=(isset($discount) ? ($discount->apply == 2 ? 'checked'  : '') : ''); ?> type='radio' style="width: 60px;" id='radio_2' name='apply' value='2' />&nbsp;&nbsp;<label><?=_l('dont_active')?></label>
													    </div>
												    </div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
			<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 ">
				<ul class="nav nav-tabs profile-tabs" role="tablist">
                    <li role="presentation" class="itemss active">
                        <a href="#items" class="js-btn-tab" aria-controls="items" role="tab" data-toggle="tab">
                        	Danh mục hàng hóa
                        </a>
                    </li>
                    <li role="presentation" class="product">
                        <a href="#product" class="js-btn-tab" aria-controls="product" role="tab" data-toggle="tab">
                        	Danh mục thành phẩm
                        </a>
                    </li>
                    <li role="presentation" class="clientss <?=(isset($discount) ? ($discount->id_client == 2 ? ''  : 'hide') : 'hide'); ?>">
                        <a href="#client" class="js-btn-tab" aria-controls="client" role="tab" data-toggle="tab">
                        	Khách hàng áp dụng
                        </a>
                    </li>
                 </ul>
                 <div class="tab-content">
             <div role="tabpanel" class="tab-pane" id="product">
				<table id="example_product"  style="min-height: 400px" class="table table-striped  table-discount_product table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center" >STT</th>
							<th class="text-center" style="width: 30%">Tên nhóm</th>
							<th class="text-center" style="width: 15%">Cấp độ</th>
							<th class="text-center" style="width: 15%">Chiết khấu</th>
							<th class="text-center hide" style="width: 15%">Số lượng</th>
							<th class="text-center" style="width: 25%">Ghi chú</th>
						</tr>
					</thead>
					<tbody>
					<?php $i=0; foreach ($categories_product as $key => $value) { ?>
						<?php
						$readonly = '';
						$hide = 'hide';
						$hidee='';
						if(!empty($discount)){
							if($discount->items_product[$value['id']]['readonly'] == 2)
							{
							$readonly='';
							$hide='';	
							}elseif($discount->items_product[$value['id']]['readonly'] == 1)
							{
							$readonly = 'readonly';
							$hide='no-drop-v2';	
							$hidee='no-drop-v2';	
							}elseif($discount->items_product[$value['id']]['readonly'] == 3)
							{
							$hidee='hide';	
							}
						}
						 ?>
						<tr class="item_product parent__<?=$value['id']?> <?=$value['class']?>">
							<?php if($value['lever'] == 1){?>
							<td class="text-center"><i class="lnr lnr-exit" style="color: #ff6f00"></i></td>
							<?php }else{?>
							<td class="text-center"></td>
							<?php }?>
							<td ><input class="hide" name="items_product[<?=$i?>][id]" value="<?=$value['id']?>"><?=$value['name']?></td>
							<td class="text-center"><?=_l('ch_levers')?> <?=$value['lever']?></td>
							<td><input class="hide readonly_product"  name="items_product[<?=$i?>][readonly_product]" value="<?= (isset($discount) ? ($discount->items_product[$value['id']]['readonly'] > 0 ? $discount->items_product[$value['id']]['readonly'] : 0) : 0); ?>"><input <?=$readonly?>  data-id="<?=$value['id']?>" value="<?= (isset($discount) ? ($discount->items_product[$value['id']]['discounts'] > 0 ? $discount->items_product[$value['id']]['discounts'] : '') : ''); ?>" data-idd="<?=$value['idd']?>" type="number" max-hau="100" min-hau="0" class="H_input discounts_product <?=$hidee?>" name="items_product[<?=$i?>][discounts_product]"></td>
							<td class="hide" ><input <?=$readonly?> value="<?= (isset($discount) ? ($discount->items_product[$value['id']]['quantity'] > 0 ? $discount->items_product[$value['id']]['quantity'] : '') : ''); ?>" data-id="<?=$value['id']?>" data-idd="<?=$value['idd']?>" type="number" min="0" class="H_input quantity_product <?=$hide?>" name="items_product[<?=$i?>][quantity_product]" value="1"></td>
							<td><textarea rows="2" id="note" name="items_product[<?=$i?>][note]" class="form-control " value=""><?= (isset($discount) ? $discount->items_product[$value['id']]['note'] : ''); ?></textarea></td>
						</tr>
					<?php $i++; } ?>
					</tbody>
				</table>
			</div>                 	
             <div role="tabpanel" class="tab-pane active" id="items">
				<table id="example"  style="min-height: 400px" class="table table-striped  table-discount table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center" >STT</th>
							<th class="text-center" style="width: 20%">Tên nhóm</th>
							<th class="text-center" style="width: 15%">Cấp độ</th>
							<th class="text-center" style="width: 15%">Chiết khấu (%)</th>
							<th class="text-center" style="width: 15%">Chiết khấu (TM)</th>
							<th class="text-center hide" style="width: 10%">Số lượng</th>
							<th class="text-center" style="width: 25%">Ghi chú</th>
						</tr>
					</thead>
					<tbody>
					<?php $i=0; foreach ($categories as $key => $value) { ?>
						<?php
						$readonly = '';
						$hide = 'hide';
						$hidee='';
						if(!empty($discount)){
							if($discount->items[$value['id']]['readonly'] == 2)
							{
							$readonly='';
							$hide='';	
							}elseif($discount->items[$value['id']]['readonly'] == 1)
							{
							$readonly = 'readonly';
							$hide='no-drop-v2';	
							$hidee='no-drop-v2';	
							}elseif($discount->items[$value['id']]['readonly'] == 3)
							{
							$hidee='hide';	
							}
						}
						 ?>
						<tr class="item parent__<?=$value['id']?> <?=$value['class']?>">
							<?php if($value['lever'] == 1){?>
							<td class="text-center"><i class="lnr lnr-exit" style="color: #ff6f00"></i></td>
							<?php }else{?>
							<td class="text-center"></td>
							<?php }?>
							<td ><input class="hide" name="items[<?=$i?>][id]" value="<?=$value['id']?>"><?=$value['category']?></td>
							<td class="text-center"><?=_l('ch_levers')?> <?=$value['lever']?></td>
							<td><input class="hide readonly"  name="items[<?=$i?>][readonly]" value="<?= (isset($discount) ? ($discount->items[$value['id']]['readonly'] > 0 ? $discount->items[$value['id']]['readonly'] : 0) : 0); ?>"><input <?=$readonly?>  data-id="<?=$value['id']?>" value="<?= (isset($discount) ? ($discount->items[$value['id']]['discounts'] > 0 ? $discount->items[$value['id']]['discounts'] : '') : ''); ?>" data-idd="<?=$value['idd']?>" type="number" max-hau="100" min-hau="0" class="H_input discounts <?=$hidee?>" name="items[<?=$i?>][discounts]"></td>
							<td>
								<input type="text" class="H_input discounts_cash" name="items[<?=$i?>][discounts_cash]" onkeyup="formatNumBerKeyUp(this)" data-parent="<?=$value['class']?>">
							</td>
							<td  class="hide"><input <?=$readonly?> value="<?= (isset($discount) ? ($discount->items[$value['id']]['quantity'] > 0 ? $discount->items[$value['id']]['quantity'] : '') : ''); ?>" data-id="<?=$value['id']?>" data-idd="<?=$value['idd']?>" type="number" min="0" class="H_input quantity <?=$hide?>" name="items[<?=$i?>][quantity]" value="1"></td>
							<td><textarea rows="2" id="note" name="items[<?=$i?>][note]" class="form-control " value=""><?= (isset($discount) ? $discount->items[$value['id']]['note'] : ''); ?></textarea></td>
						</tr>
					<?php $i++; } ?>
						
					</tbody>
				</table>
			</div>
					<div role="tabpanel" class="tab-pane" id="client">
							<table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
									<tbody>
										<tr>
											<td style="width: 30%">
												<label for="number" class="control-label">
													<?php echo _l('Khách hàng'); ?>
												</label>
											</td>
											<td style="width: 70%">
												<input data-placeholder="<?=_l('Danh sách khách hàng')?>" id="client_id" class="client_id" style="width: 100%" >
											</td>
										</tr>
									</tbody>
							</table>
						<div class="clearfix"></div>
						<br>
						<table id="inventory" class="table table-striped  item-inventory table-bordered" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th><?php echo _l('Mã khách hàng'); ?></th>
									<th ><?php echo _l('Tên khách hàng'); ?></th>
									<th ><?php echo _l('SDT'); ?></th>
									<th ><?php echo _l('Địa chỉ'); ?></th>
									<th><i class="fa fa-trash-o" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody>
						<?php $j=0; 
						if(!empty($discount)){
						foreach ($discount->clients as $key => $value) { ?>
							<tr>
								<td><input class="id hide" name="client[<?=$j?>][id_client]" value="<?=$value['id']?>"> <?=$value['code_clients']?> </td>
								<td ><?=$value['text']?></td>
								<td ><?=$value['phonenumber']?></td>
								<td ><?=$value['address']?></td>
								<td ><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
							</tr>
						<?php $j++; } }?>
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
					<div class="width90 pull-left">
						<!-- <table class="table tnh-tb noMargin table-color_sum dont-responsive-table">
							<tbody>
								<tr>
									<td><span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
									</td>
									<td class="total_quantity_all">
										<?php echo $totalQuantity ?>
									</td>
									<td><span class="bold"><?php echo _l('item_quantity_approve'); ?> :</span>
									</td>
									<td class="total_quantity_approve">
										<?php echo $totalQuantity_approve ?>
									</td>
								</tr>
							</tbody>
						</table> -->
					</div>
		            <button class="btn btn-info pull-right only-save customer-form-submiter">
		            <?php echo _l( 'submit'); ?>
		            </button>
		        </div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<!-- //nhớ tải về  -->
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
	<link href="https://datatables.net/download/build/dataTables.responsive.nightly.css" rel="stylesheet" type="text/css" />
	<script src="https://datatables.net/download/build/dataTables.responsive.nightly.js"></script>
<script>
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust();
});
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
                                    type:$('[name="type_client[]"]').val(),
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
                                    type:$('[name="type_client[]"]').val(),
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
    function repoFormatSelection(state) {
        return  '['+state.code_clients +'] '+ state.text ;
    }
    ajaxSelectCallBack($('.client_id'), "<?=admin_url('discount/SearchClient')?>", 0);
				var t;
				$(document).ready(function() {	
					t = $('#inventory').DataTable(
						{
						responsive : true,
						scrollY:        '30vh',
					    "oLanguage":{
						    "sProcessing":   "Đang xử lý...",
						    "sLengthMenu":   "Xem _MENU_ mục",
						    "sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
						    "sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
						    "sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
						    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
						    "sInfoPostFix":  "",
						    "sSearch":       "Tìm:",
						    "sUrl":          "",
						    "oPaginate": {
						        "sFirst":    "Đầu",
						        "sPrevious": "Trước",
						        "sNext":     "Tiếp",
						        "sLast":     "Cuối"
						    }
						},
						pageLength:50
						}
						);
				});	
		        $('.client_id').on('change', function(e){
		               createTrItem();
		        });
		        var uniqueArray  = <?php echo $j?>;
    		   	function createTrItem()
			    {
			    	var name = $('#client_id').select2('data').text;
					var id = $('#client_id').select2('data').id;
					var address = $('#client_id').select2('data').address;
					var phonenumber = $('#client_id').select2('data').phonenumber;
					var code_clients = $('#client_id').select2('data').code_clients;
			        if($('table.item-inventory tbody tr').find('input[value=' + id + '].id ').length > 0) {
			        	alert_float('warning', "Khách hàng đã được thêm! Vui lòng kiểm tra lại");
			            return;	
			        }
		t.row.add([
            '<input class="id hide" name="client[' + uniqueArray + '][id_client]" value="'+id+'">['+code_clients+']',
            name,
            phonenumber,
            address,
            '<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a>'
        ]).draw(false);
        uniqueArray++;
			    };
				var deleteTrItem = (trItem) => {
					var r = confirm("<?php echo _l('confirm_action_prompt');?>");
				    if (r == false) {
				        return false;
				    } else {
					t
				        .row( $(trItem).parents('tr') )
				        .remove()
				        .draw();
			    	}
			    };    
	$(document).ready(function() {	
	_validate_form($('#discount-form'), {
        date: "required",
        name_discount: "required",
        effective_date: "required",
        type_client: "required",
    });
		$('#example').dataTable( {
			responsive : true,
		    "order": [],
		    "columnDefs": [ {
		      "targets"  : [0,1,2,3,4,5],
		      "orderable": false,
		    }],
		    scrollY:        '43vh',
		    "oLanguage":{
			    "sProcessing":   "Đang xử lý...",
			    "sLengthMenu":   "Xem _MENU_ mục",
			    "sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
			    "sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
			    "sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
			    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
			    "sInfoPostFix":  "",
			    "sSearch":       "Tìm:",
			    "sUrl":          "",
			    "oPaginate": {
			        "sFirst":    "Đầu",
			        "sPrevious": "Trước",
			        "sNext":     "Tiếp",
			        "sLast":     "Cuối"
			    }
			},
			// pageLength:100,
			"bLengthChange": false,
			iDisplayLength: -1
		});
	});
	$(document).ready(function() {	
		$('#example_product').dataTable( {
			responsive : true,
		    "order": [],
		    "columnDefs": [ {
		      "targets"  : [0,1,2,3,4,5],
		      "orderable": false,
		    }],
		    scrollY:        '43vh',
		    "oLanguage":{
			    "sProcessing":   "Đang xử lý...",
			    "sLengthMenu":   "Xem _MENU_ mục",
			    "sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
			    "sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
			    "sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
			    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
			    "sInfoPostFix":  "",
			    "sSearch":       "Tìm:",
			    "sUrl":          "",
			    "oPaginate": {
			        "sFirst":    "Đầu",
			        "sPrevious": "Trước",
			        "sNext":     "Tiếp",
			        "sLast":     "Cuối"
			    }
			},
			// pageLength:100,
			"bLengthChange": false,
			iDisplayLength: -1
		});
	});
	    var ch_daterangepicker = () => {
          $('input[name="effective_date"]').daterangepicker({
            opens: 'left',
            isInvalidDate: false,
            "locale": {
                    "format": "DD/MM/YYYY",
                    "separator": " - ",
                    "applyLabel": lang_daterangepicker.applyLabel,
                    "cancelLabel": lang_daterangepicker.cancelLabel,
                    "fromLabel": lang_daterangepicker.fromLabel,
                    "toLabel": lang_daterangepicker.toLabel,
                    "customRangeLabel": lang_daterangepicker.customRangeLabel,
                    "daysOfWeek": lang_daterangepicker.daysOfWeek,
                    "monthNames": lang_daterangepicker.monthNames
                },
          }, function(start, end, label) {
          });
          // $('input[name="effective_date"]').val('').datepicker("refresh");
          // $('input[name="effective_date"]').on('apply.daterangepicker', function(ev, picker) {
          // $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
          // $( "#effective_date" ).trigger( "change" );
          //  });
          // $('input[name="effective_date"]').on('cancel.daterangepicker', function(ev, picker) {
          //     $(this).val('');
          //     $( "#effective_date" ).trigger( "change" );
          //  });
        };
        ch_daterangepicker();
        // function quantity() {
        	
        // }
        $('.quantity').on('change', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').val(input.val());
                input.attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').val('');
            	}
            if(input.val() < 1)
            {
            	input.val(1);
            	input.change();
            }	
        });
        $('.quantity').on('click', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').val(input.val());
                input.attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').val('');
            	}
            if(input.val() < 1)
            {
            	input.val(1);
            	input.change();
            }	
        });        
        $('.quantity').on('keyup', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').val(input.val());
                input.attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').val('');
            	}
            if(input.val() < 1)
            {
            	input.val(1);
            	input.change();
            }	
        });        
        $('.discounts').on('keyup', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-idd').split(',');
               $.each(parent, (index,value)=>{
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').attr('readonly', true);
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').addClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.readonly').val(3);
                input.attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
                input.parent().parent('tr').find('td > input.quantity').attr('readonly', false);
                input.parent().parent('tr').find('td > input.quantity').removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	var discountss = $('table.table-discount tbody').find('tr.parent_'+value).find('td > input.discounts');
            	var strue = true;
            	$.each(discountss, (index,value)=>{
            		if($(value).val() > 0)
            		{
            			strue = false;
            		}
            	});
            	if(strue){
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.readonly').val(0);
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').removeClass('hide no-drop-v2	');
            	}
            	input.parent().parent('tr').find('td > input.quantity').addClass('hide');
            	input.parent().parent('tr').find('td > input.quantity').val(1);
            	input.parent().parent('tr').find('td > input.quantity').change();
            	}
            })
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').attr('readonly', true);
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').addClass(' no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').val(input.val());
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.readonly').val(1);
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').removeClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').attr('readonly', true);
               // $('table.table-discount tbody').find('tr.parent_'+parent).css({'background-color':'#bbbbbb'});
                input.attr('readonly', false);
                input.parent().find('input.readonly').val(2);
                input.parent().parent('tr').find('td > input.quantity').attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.readonly').val(0);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').addClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').removeClass(' no-drop-v2');
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').val('');
            	}
            	if(input.val() < 0)
	            {
	            	input.val(1);
	            	input.change();
	            }
	            if(input.val() > 100)
	            {
	            	input.val(100);
	            	input.change();
	            }
        });        
        $('.discounts').on('click', function(e){
               var input =  $(e.currentTarget);
               if(input.hasClass('no-drop-v2')){
               	return;
               }
               var parent = input.attr('data-idd').split(',');
               $.each(parent, (index,value)=>{
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').attr('readonly', true);
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').addClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.readonly').val(3);
                input.attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
                input.parent().parent('tr').find('td > input.quantity').attr('readonly', false);
                input.parent().parent('tr').find('td > input.quantity').removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	var discountss = $('table.table-discount tbody').find('tr.parent_'+value).find('td > input.discounts');
            	var strue = true;
            	$.each(discountss, (index,value)=>{
            		if($(value).val() > 0)
            		{
            			strue = false;
            		}
            	});
            	if(strue){
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.readonly').val(0);
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').removeClass('hide no-drop-v2	');
            	}
            	input.parent().parent('tr').find('td > input.quantity').addClass('hide');
            	input.parent().parent('tr').find('td > input.quantity').val(1);
            	input.parent().parent('tr').find('td > input.quantity').change();
            	}
            })
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').attr('readonly', true);
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').addClass(' no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').val(input.val());
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.readonly').val(1);
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').removeClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').attr('readonly', true);
               // $('table.table-discount tbody').find('tr.parent_'+parent).css({'background-color':'#bbbbbb'});
                input.attr('readonly', false);
                input.parent().find('input.readonly').val(2);
                input.parent().parent('tr').find('td > input.quantity').attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.readonly').val(0);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').addClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').removeClass(' no-drop-v2');
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').val('');
            	}
            	if(input.val() < 0)
	            {
	            	input.val(1);
	            	input.change();
	            }
	            if(input.val() > 100)
	            {
	            	input.val(100);
	            	input.change();
	            }
        });
        $('.discounts').on('change', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-idd').split(',');
               $.each(parent, (index,value)=>{
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').attr('readonly', true);
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').addClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent__'+value).find('td > input.readonly').val(3);
                input.attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
                input.parent().parent('tr').find('td > input.quantity').attr('readonly', false);
                input.parent().parent('tr').find('td > input.quantity').removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	var discountss = $('table.table-discount tbody').find('tr.parent_'+value).find('td > input.discounts');
            	var strue = true;
            	$.each(discountss, (index,value)=>{
            		if($(value).val() > 0)
            		{
            			strue = false;
            		}
            	});
            	if(strue){
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.readonly').val(0);
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent__'+value).find('td > input.discounts').removeClass('hide no-drop-v2	');
            	}
            	input.parent().parent('tr').find('td > input.quantity').addClass('hide');
            	input.parent().parent('tr').find('td > input.quantity').val(1);
            	input.parent().parent('tr').find('td > input.quantity').change();
            	}
            })
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').attr('readonly', true);
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').addClass(' no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').val(input.val());
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.readonly').val(1);
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').removeClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').attr('readonly', true);
               // $('table.table-discount tbody').find('tr.parent_'+parent).css({'background-color':'#bbbbbb'});
                input.attr('readonly', false);
                input.parent().find('input.readonly').val(2);
                input.parent().parent('tr').find('td > input.quantity').attr('readonly', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.readonly').val(0);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').addClass('hide no-drop-v2');
               $('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.quantity').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').attr('readonly', false);
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').removeClass(' no-drop-v2');
            	$('table.table-discount tbody').find('tr.parent_'+parent).find('td > input.discounts').val('');
            	}
            	if(input.val() < 0)
	            {
	            	input.val(1);
	            	input.change();
	            }
	            if(input.val() > 100)
	            {
	            	input.val(100);
	            	input.change();
	            }
        });
        $('[name="id_client"]').on('change', function(e){
        	var input =  $(e.currentTarget);
        	if(input.val() == 2)
        	{
        		$('.clientss').removeClass('hide');
        	}else if(input.val() == 1){
        		$('.clientss').addClass('hide');
        	}
        });
        $('.quantity_product').on('change', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').val(input.val());
                input.attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').val('');
            	}
            if(input.val() < 1)
            {
            	input.val(1);
            	input.change();
            }	
        });
        $('.quantity_product').on('click', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').val(input.val());
                input.attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').val('');
            	}
            if(input.val() < 1)
            {
            	input.val(1);
            	input.change();
            }	
        });        
        $('.quantity_product').on('keyup', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').val(input.val());
                input.attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').val('');
            	}
            if(input.val() < 1)
            {
            	input.val(1);
            	input.change();
            }	
        });        
        $('.discounts_product').on('keyup', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-idd').split(',');
               $.each(parent, (index,value)=>{
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').attr('readonly_product', true);
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').addClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.readonly_product').val(3);
                input.attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
                input.parent().parent('tr').find('td > input.quantity_product').attr('readonly_product', false);
                input.parent().parent('tr').find('td > input.quantity_product').removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	var discounts_products = $('table.table-discount_product tbody').find('tr.parent_'+value).find('td > input.discounts_product');
            	var strue = true;
            	$.each(discounts_products, (index,value)=>{
            		if($(value).val() > 0)
            		{
            			strue = false;
            		}
            	});
            	if(strue){
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.readonly_product').val(0);
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').removeClass('hide no-drop-v2	');
            	}
            	input.parent().parent('tr').find('td > input.quantity_product').addClass('hide');
            	input.parent().parent('tr').find('td > input.quantity_product').val(1);
            	input.parent().parent('tr').find('td > input.quantity_product').change();
            	}
            })
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').attr('readonly_product', true);
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').addClass(' no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').val(input.val());
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.readonly_product').val(1);
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').removeClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').attr('readonly_product', true);
               // $('table.table-discount_product tbody').find('tr.parent_'+parent).css({'background-color':'#bbbbbb'});
                input.attr('readonly_product', false);
                input.parent().find('input.readonly_product').val(2);
                input.parent().parent('tr').find('td > input.quantity_product').attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.readonly_product').val(0);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').addClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').removeClass(' no-drop-v2');
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').val('');
            	}
            	if(input.val() < 0)
	            {
	            	input.val(1);
	            	input.change();
	            }
	            if(input.val() > 100)
	            {
	            	input.val(100);
	            	input.change();
	            }
        });        
        $('.discounts_product').on('click', function(e){
               var input =  $(e.currentTarget);
               if(input.hasClass('no-drop-v2')){
               	return;
               }
               var parent = input.attr('data-idd').split(',');
               $.each(parent, (index,value)=>{
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').attr('readonly_product', true);
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').addClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.readonly_product').val(3);
                input.attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
                input.parent().parent('tr').find('td > input.quantity_product').attr('readonly_product', false);
                input.parent().parent('tr').find('td > input.quantity_product').removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	var discounts_products = $('table.table-discount_product tbody').find('tr.parent_'+value).find('td > input.discounts_product');
            	var strue = true;
            	$.each(discounts_products, (index,value)=>{
            		if($(value).val() > 0)
            		{
            			strue = false;
            		}
            	});
            	if(strue){
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.readonly_product').val(0);
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').removeClass('hide no-drop-v2	');
            	}
            	input.parent().parent('tr').find('td > input.quantity_product').addClass('hide');
            	input.parent().parent('tr').find('td > input.quantity_product').val(1);
            	input.parent().parent('tr').find('td > input.quantity_product').change();
            	}
            })
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').attr('readonly_product', true);
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').addClass(' no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').val(input.val());
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.readonly_product').val(1);
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').removeClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').attr('readonly_product', true);
               // $('table.table-discount_product tbody').find('tr.parent_'+parent).css({'background-color':'#bbbbbb'});
                input.attr('readonly_product', false);
                input.parent().find('input.readonly_product').val(2);
                input.parent().parent('tr').find('td > input.quantity_product').attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.readonly_product').val(0);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').addClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').removeClass(' no-drop-v2');
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').val('');
            	}
            	if(input.val() < 0)
	            {
	            	input.val(1);
	            	input.change();
	            }
	            if(input.val() > 100)
	            {
	            	input.val(100);
	            	input.change();
	            }
        });
        $('.discounts_product').on('change', function(e){
               var input =  $(e.currentTarget);
               var parent = input.attr('data-idd').split(',');
               $.each(parent, (index,value)=>{
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').attr('readonly_product', true);
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').addClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.readonly_product').val(3);
                input.attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
                input.parent().parent('tr').find('td > input.quantity_product').attr('readonly_product', false);
                input.parent().parent('tr').find('td > input.quantity_product').removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	var discounts_products = $('table.table-discount_product tbody').find('tr.parent_'+value).find('td > input.discounts_product');
            	var strue = true;
            	$.each(discounts_products, (index,value)=>{
            		if($(value).val() > 0)
            		{
            			strue = false;
            		}
            	});
            	if(strue){
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.readonly_product').val(0);
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent__'+value).find('td > input.discounts_product').removeClass('hide no-drop-v2	');
            	}
            	input.parent().parent('tr').find('td > input.quantity_product').addClass('hide');
            	input.parent().parent('tr').find('td > input.quantity_product').val(1);
            	input.parent().parent('tr').find('td > input.quantity_product').change();
            	}
            })
               var parent = input.attr('data-id');
               if(!empty(input.val())){
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').attr('readonly_product', true);
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').addClass(' no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').val(input.val());
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.readonly_product').val(1);
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').removeClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').attr('readonly_product', true);
               // $('table.table-discount_product tbody').find('tr.parent_'+parent).css({'background-color':'#bbbbbb'});
                input.attr('readonly_product', false);
                input.parent().find('input.readonly_product').val(2);
                input.parent().parent('tr').find('td > input.quantity_product').attr('readonly_product', false);
                input.removeClass('no-drop-v2 hide');
            	}else  if(empty(input.val()))
            	{
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.readonly_product').val(0);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').addClass('hide no-drop-v2');
               $('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.quantity_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').attr('readonly_product', false);
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').removeClass(' no-drop-v2');
            	$('table.table-discount_product tbody').find('tr.parent_'+parent).find('td > input.discounts_product').val('');
            	}
            	if(input.val() < 0)
	            {
	            	input.val(1);
	            	input.change();
	            }
	            if(input.val() > 100)
	            {
	            	input.val(100);
	            	input.change();
	            }
        });
        // var items = $('table.table-discount tbody').find('tr.item');
		// $.each(items, (index,value)=>{
		// 	$(value).find('td > input.discounts').change();
		// });

		$('.discounts_cash').on('change', function(e){
			var current =  $(e.currentTarget);
			if(current.val()) {
				var id_parent = current.attr('data-parent');
				var arr_id_parent = id_parent.split(" ");
				var all_child = $('.discounts_cash');
				$.each(all_child, (i, v)=>{
					var arr_id_child = $(v).attr('data-parent').split(" ");
					for (var i = 0; i < arr_id_child.length; i++) {
						if(arr_id_child[i] == arr_id_parent[0]) {
							$(v).val(current.val());
							$(v).attr('readonly',true);
							$(v).addClass('no-drop-v2');
						}

						for (var j = 0; j < arr_id_parent.length; j++) {
							
						}
					}
				});
				current.attr('readonly',false);
				current.removeClass('no-drop-v2');
			}
			else {
				var id_parent = current.attr('data-parent');
				var arr_id_parent = id_parent.split(" ");
				var all_child = $('.discounts_cash');
				$.each(all_child, (i, v)=>{
					var arr_id_child = $(v).attr('data-parent').split(" ");
					for (var i = 0; i < arr_id_child.length; i++) {
						if(arr_id_child[i] == arr_id_parent[0]) {
							$(v).val(current.val());
							$(v).attr('readonly',false);
							$(v).removeClass('no-drop-v2');
						}
					}
				});
				current.attr('readonly',false);
				current.removeClass('no-drop-v2');
			}
		});
</script>