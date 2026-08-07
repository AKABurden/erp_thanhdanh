<?php init_head(); ?>
<style type="text/css">
    .c_img_item{
        border-radius: 50%;
        width: 70px;
        height: 70px;
    }
    .width100{
        width:80px!important;
    }
    .width130{
        width:100px!important;
    }
    .minwidth150px{
        min-width: 230px;
    }
    div.c_product_items{
        width: 100%!important;
        margin-top: 10px;
    }
    tr div.c_customer {
        width: 100%!important;
        margin-top: 10px;
    }
    .group_addon{
        padding: 0px 0px 0px 5px!important;
        border: none!important;
    }
    .form-control{
        height: 42px;
    }
    .radio.pull-right
    {
        height: 8px;
    }
    .c_price, .c_total, .total_cost_trans, .total_orders, .c_guest_giving, .c_number_items, .c_cost_trans{
        text-align: right!important;
    }
    .c_size{
        text-align: left!important;
    }
    .c_guest_giving{
        padding-right: 0px!important;
    }
    .ribbon span{
        font-size: 8px;
    }
    #table-item-orders tbody tr td{
        vertical-align: middle;
    }
    .plef0{
        padding-left: 0px!important;
    }
    .pright0{
        padding-right: 0px!important;
    }
    .pright5{
        padding-right: 5px!important;
    }
    input[type="number"]{
        height: 42px;
    }
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
            if (empty($quotes_orders) || (!empty($quotes_orders) && $quotes_orders->status == 0)){
                $action = true;
            }

            if(!empty($action))
            {
			    echo form_open($this->uri->uri_string(), array('id' => 'quotes_orders-form', 'class' => '_transaction_form orders-form'));
            }
			?>
			<div>
				<div class="panel_s">
				 	<div class="additional"></div>
				 	<div class="panel-body">
				 	<?php 
						$type = '';
						if (!isset($quotes_orders))
							$type = 'info';
						elseif ($quotes_orders->status == 0)
							$type = 'warning';
						elseif ($quotes_orders->status == 2)
							$type = 'danger';

						?>
				 		<div class="ribbon <?= $type ?>">
				 			<?php 
								if(!isset($quotes_orders))
								{
									$status = _l('cong_add_new');
								}
								else
								{
//									$status = $quotes_orders->name_status;
									$status = "";
								}
							?>
				 			<span> <?= $status ?> </span>
						 </div>
						<h4 class="bold no-margin font-medium">
					     	<?php echo (!empty($title) ? $title : ''); ?>
                            <span class="label label-<?=!empty($type) ? $type : '' ?> mleft5 inline-block pointer">
                                <?=!empty($status) ? $status : $title ?>
                            </span>
					   	</h4>
						<hr />
				 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				 			<div class="panel panel-primary">
								<div class="panel-heading"><?=_l('lead_general_info')?></div>
								<div class="panel-body">
                                    <?php if(!empty($quotes_orders)){ ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                 <label for="number"><?php echo _l('ch_code_p'); ?></label>
                                                 <div class="input-group">
                                                  <span class="input-group-addon"> <?php echo (isset($quotes_orders) ? ($quotes_orders->prefix) : '');?>-</span>
                                                    <?php
                                                            $value = (isset($quotes_orders) ? ($quotes_orders->code) : '');
                                                     ?>
                                                    <input type="text" class="form-control" value="<?= $value ?>" readonly>
                                                  </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="col-md-3">
                                        <?php
                                            $value = (isset($quotes_orders) ? $quotes_orders->client : '');
                                            echo render_select('client', $clients, array('userid', 'company'), 'cong_client', $value, [], [],'', 'c_customer with-ajax search-ajax');
                                        ?>
                                    </div>
                                    <div  class="col-md-3">
                                        <?php
                                            $value = (isset($quotes_orders) ? $quotes_orders->shipping : '');
//                                            echo render_select('shipping', $shipping, array('id', 'name', 'address'), 'cong_shipping', $value, [], [],'', '_select_input_group');
                                            echo render_select_with_input_group('shipping', $shipping, ['id', 'name', 'address'], 'cong_shipping', $value, '<a href="#" onclick="ViewShippingClient(\'shipping\')"><i class="fa fa-plus"></i></a>', ['data-actions-box' => true], [], '', '', false);
                                        ?>
                                    </div>
						            <div class="col-md-3">
						                <?php $value = (isset($quotes_orders) ? _d($quotes_orders->date) : _d(date('Y-m-d'))); ?>
			                  			<?php echo render_date_input('date', 'cong_date_month', $value); ?>
                                        <!-- ngày thực hiện-->
			                  		</div>
                                    <div class="col-md-3">
			                  			<?php
											$value = (isset($quotes_orders) ? $quotes_orders->assigned : '');
											echo render_select('assigned', $staff, ['staffid', ['lastname', 'firstname']], 'cong_assigned', $value);
										?>
			                  		</div>
			                  		<div class="col-md-12">
										<?php $value = (isset($quotes_orders) ? $quotes_orders->note : ""); ?>
										<?php echo render_textarea('note', 'cong_note', $value); ?>
									</div>
								</div>
							</div>
                            <div class="panel panel-info">
                                <div class="panel-heading"><?=_l('cong_info_items')?></div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table-item-quotes_orders">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <a onclick="AddItemOrder()" class="btn btn-info btn-icon">+</a>
                                                    </th>
                                                    <th>
                                                        <?=_l('cong_item_code')?>
                                                    </th> <!--mã hàng-->
                                                    <th><?=_l('cong_item_image')?></th> <!--Hình ảnh-->
                                                    <th><?=_l('cong_item_name')?></th> <!--Tên hàng-->
                                                    <th><?=_l('cong_quantity')?></th> <!--Số lượng -->
                                                    <th><?=_l('cong_price_thinh')?></th> <!--Giá thỉnh-->
                                                    <th><?=_l('cong_discount')?></th> <!--Chiết khấu-->
                                                    <th><?=_l('cong_cost_trans')?></th> <!--Phí vận chuyển-->
                                                    <th><?=_l('cong_info_money')?></th> <!--Thành tiền-->
                                                    <th><?=_l('cong_buy_gif')?></th> <!--mua cho-->
                                                    <th><?=_l('cong_size')?></th>
                                                    <th><?=_l('options')?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php include_once(APPPATH.'views/admin/quotes_orders/TrUpdate.php'); ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-offset-6 col-md-6 mtop10">
                                        <div class="panel panel-info">
                                            <div class="panel-heading"><?=_l('cong_info_pay')?></div>
                                            <div class="panel-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th><?=_l('cong_info_items')?></th>
                                                            <th class="text-center"><?=_l('VND')?></th>
                                                            <th><?=_l('cong_currencies')?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <p><?=_l('cong_num_item_order')?>:</p> <!-- Số mặt hàng trong giõ hàng-->
                                                            </td>
                                                            <td>
                                                                <p class="c_number_items"><?=!empty($quotes_orders->total_item) ? number_format($quotes_orders->total_item) : '' ?></p> <!-- Số mặt hàng trong giõ hàng-->
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <p><?=_l('cong_cost_trans')?>:</p> <!-- Tổng chi phí vận chuyễn -->
                                                            </td>
                                                            <td>
                                                                <p class="total_cost_trans"> <?=!empty($quotes_orders->total_cost_trans) ? number_format($quotes_orders->total_cost_trans) : '' ?></p> <!-- Tổng chi phí vận chuyễn -->
                                                            </td>
                                                            <td>
                                                                <p></p> <!-- Tổng chi phí vận chuyễn -->
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <p><?= _l('cong_total_quotes_orders')?>:</p> <!-- Tổng giá trị đơn hàng -->
                                                            </td>
                                                            <td>
                                                                <p class="total_quotes_orders text-right"> <?=!empty($quotes_orders->grand_total) ? number_format($quotes_orders->grand_total) : '' ?> </p> <!-- Tổng giá trị đơn hàng -->
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
						<div class="clearfix"></div>
				 		</div>
				 	</div>
			 	</div>
            <?php if(!empty($action)){?>
                <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                    <button class="btn btn-info only-save customer-form-submiter">
                        <?php echo _l( 'submit'); ?>
                    </button>
                </div>
            <?php } ?>
			</div>
			
			<?php
            if(!empty($action)) {
                echo form_close();
            }
            ?>
		</div>
	</div>
</div>


<?php init_tail(); ?>

<script>

    var ValdateFrom = {
        client: 'required',
        date: 'required',
        assigned: 'required',
        shipping: 'required'
    };
    if(vValdateFrom)
    {
        $.each(vValdateFrom, function(iV, vV){
            ValdateFrom[iV] = vV;
        })
    }

    $(function() {
        appValidateForm($('#quotes_orders-form'), ValdateFrom);
    })
    var pls_option_select = "<?=_l('cong_pls_selected_option')?>";
    var lang_money = "<?=_l('cong_money')?>";
    $(window).bind("load", function() {
        selectpicker_jax_item();
        selectpicker_jax_customer();
    })
    var UnitArray = 1;

    //Add items
    function AddItemOrder()
    {
        var _Tr = $('<tr class="strMain" index="'+UnitArray+'"></tr>');
        var td0 = $('<td></td>');
        var td1 = $('<td></td>');
        var SelectItem = $(
            '<div class="show-types"></div>'+
            '<input type="hidden" name="items['+UnitArray+'][type_items]" id="items['+UnitArray+'][type_items]" class="form-control type_items" value="">'+
            '<select class="c_product_items selectpicker search-ajax with-ajax" id="items['+UnitArray+'][id_product]"  data-live-search="true" name="items['+UnitArray+'][id_product]"> </select>');
        td1.append(SelectItem);

        var td2 = $('<td class="text-center"><img src="<?=base_url()?>assets/images/preview-not-available.jpg" class="c_img_item"></td>');
        var td3 = $('<td><p class="name_product mtop20"></p></td>');
        var td4 = $('<td><input class="form-control c_quantity width100 H_input" min="1" onkeyup="C_formatNumBerKeyUp(this)" name="items['+UnitArray+'][quantity]" id="items['+UnitArray+'][quantity]" value="1"></td>');
        var td5 = $('<td><input class="form-control c_price width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items['+UnitArray+'][price]" id="items['+UnitArray+'][price]"></td>');
        var td6 = $('<td></td>');
        var div_input_group_td6 = $('<div class="input-group"><input class="form-control c_discount width100 H_input " name="items['+UnitArray+'][discount]"></div>');

        var div_radiot6 = $('<div class="input-group-addon group_addon"></div>');
        div_radiot6.append($('<div class="radio pull-left"><input type="radio" class="c_type_discount" name="items['+UnitArray+'][type_discount]" checked value="1"><label>%</label></div>'));
        div_radiot6.append($('<div class="radio pull-right"><input type="radio" class="c_type_discount" name="items['+UnitArray+'][type_discount]" value="2"><label>'+lang_money+'</label></div>'));
        div_input_group_td6.append(div_radiot6);
        td6.append(div_input_group_td6);

        var td7 = $('<td><input class="form-control c_cost_trans width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items['+UnitArray+'][cost_trans]" value="0"></td>');
        var td8 = $('<td><p class="c_total mtop25"></p></td>');
        var td9 = $('<td></td>');

        var SelectClient = $('<select class="c_customer search-ajax selectpicker with-ajax"  data-live-search="true" name="items['+UnitArray+'][id_customer]"> </select>');
        td9.append(SelectClient);

        var td10 = $('<td><input class="form-control c_size width100 H_input" name="items['+UnitArray+'][size]"></td>');

        var td11 = $('<td><a class="btn btn-icon btn-danger DeleteItems"><i class="fa fa-times" aria-hidden="true"></i></a></td>');
        _Tr.append(td0);
        _Tr.append(td1);
        _Tr.append(td2);
        _Tr.append(td3);
        _Tr.append(td4);
        _Tr.append(td5);
        _Tr.append(td6);
        _Tr.append(td7);
        _Tr.append(td8);
        _Tr.append(td9);
        _Tr.append(td10);
        _Tr.append(td11);

        $('#table-item-quotes_orders tbody').append(_Tr);
        selectpicker_jax_item('select[name="items['+UnitArray+'][id_product]"]');
        selectpicker_jax_customer('select[name="items['+UnitArray+'][id_customer]"]', "<?=admin_url('quotes_orders/getCustomerAjax')?>");
        UnitArray++;
        orderTR();
    }


    //Select Ajax item
    function selectpicker_jax_item(_class = "", _url = "", dataArray = {})
    {
        var _data = {};
        if (typeof(csrfData) !== 'undefined') {
            _data[csrfData['token_name']] = csrfData['hash'];
        }
        _data['q'] = "{{{q}}}";

        var options = {
            ajax: {
                // url: _url ? _url : "<?=admin_url('quotes_orders/getItemsAjax')?>",
                url: _url ? _url : "<?=admin_url('orders/getItemsAjax')?>",
                type: "POST",
                dataType: "json",
                data: _data
            },
            locale: {
                emptyTitle: pls_option_select,
                statusInitialized: pls_option_select,
            },
            log: 3,
            preserveSelected: false,
            cache: false,
            preprocessData: function(data) {
                var i,
                    l = data.length,
                    array = [];
                if (l) {
                    for (i = 0; i < l; i++) {
                        array.push(
                            $.extend(true, data[i], {
                                text: data[i].code,
                                value: data[i].id,
                                data: {
                                    subtext: data[i].name,
                                    price: data[i].price,
                                    img: data[i].avatar
                                }
                            })
                        );
                    }
                    //
                    if($(_class).length)
                    {
                        _class = _class;
                    } else {
                        _class = '.selectpicker.c_product_items.with-ajax';
                    }
                    groupSelectPicker(data, _class);
                }
                return array;
            }
        };
        if($(_class).length)
        {
            $(_class).selectpicker().filter(".with-ajax").ajaxSelectPicker(options);
        }
        else
        {
            $(".selectpicker.c_product_items.with-ajax").selectpicker().filter(".with-ajax").ajaxSelectPicker(options);
        }
    }

    //Select Ajax khách hàng
    function selectpicker_jax_customer(_class = "", _url = "", dataArray = {})
    {
        var _data = {};
        if (typeof(csrfData) !== 'undefined') {
            _data[csrfData['token_name']] = csrfData['hash'];
        }
        _data['q'] = "{{{q}}}";
        var options = {
            ajax: {
                url: _url ? _url : "<?=admin_url('quotes_orders/getCustomerAjax')?>",
                type: "POST",
                dataType: "json",
                data: _data
            },
            locale: {
                emptyTitle: pls_option_select,
                statusInitialized: pls_option_select,
            },
            log: 3,
            preserveSelected: false,
            preprocessData: function(data) {
                var i,
                    l = data.length,
                    array = [];
                if (l) {
                    for (i = 0; i < l; i++) {
                        array.push(
                            $.extend(true, data[i], {
                                text: data[i].company,
                                value: data[i].userid,
                                data: {
                                    subtext: data[i].full_code,
                                }
                            })
                        );
                    }
                }
                return array;
            }
        };
        if($(_class).length)
        {
            $(_class).selectpicker().filter(".with-ajax").ajaxSelectPicker(options);
        }
        else
        {
            $(".selectpicker.c_customer.with-ajax").selectpicker().filter(".with-ajax").ajaxSelectPicker(options);
        }
    }

    //Xóa items
    $('body').on('click', '.DeleteItems', function(e){
        if(confirm('<?=_l('cong_you_must_delete_row')?>?'))
        {
            var Tr = $(this).parents('tr');
            Tr.remove();
            ReALL();
            orderTR();
        }
    })


    //Chọn sản phẩm
    $('body').on('change', '.c_product_items', function(e){
        if($(this).val())
        {
            var Tr = $(this).parents('tr');
            var option_select = $(this).find('option:selected');

            var data_subtext = option_select.attr('data-subtext');
            var img = option_select.attr('data-img');
            var price = option_select.attr('data-price');
            //tnh
            var type_items = option_select.attr('data-types');
            Tr.find('.type_items').val(type_items);
            if (type_items == "items") {
                Tr.find('.show-types').html('<span class="label label-success"><?= lang('ch_items') ?></span>');
            } else if (type_items == "products") {
                Tr.find('.show-types').html('<span class="label label-warning"><?= lang('tnh_products') ?></span>');
            }
            //

            Tr.find('p.name_product').text(data_subtext);
            Tr.find('input.c_price').val(C_formatNumber(price));
            Tr.find('img.c_img_item').attr('src', "<?=base_url()?>"+img);

            Tr.find('input.c_quantity').trigger('change');

            var index = Tr.attr('index');
            ValdateFrom['items['+index+'][id_product]'] = 'required';
            ValdateFrom['items['+index+'][id_product]'] = 'required';
            ValdateFrom['items['+index+'][quantity]'] = {
                required : true,
                min : [1]
            };
            ValdateFrom['items['+index+'][price]'] = 'required';
            ValdateFrom['items['+index+'][price]'] = 'required';
            appValidateForm($('#quotes_orders-form'), ValdateFrom);
            if($('#table-item-quotes_orders tbody').find('tr:gt(-2)').html() == Tr.html())
            {
                AddItemOrder();
            }
        }
    })

    $('body').on('change', 'select.c_customer', function(e){
        if($(this).val())
        {
            var userid = $(this).val();
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['client'] = userid;
            $.post(admin_url+'quotes_orders/getShipping', data, function(data){
                data = JSON.parse(data);
                var option = "<option></option>";
                $.each(data, function(i, v){
                    option += '<option value="'+v.id+'" data-subtext="'+v.address+'">'+v.name+'</option>';
                })
                $('#shipping').html(option).selectpicker('refresh');
            })
        }
    })


    //Chang các input có giá trị thay đổi tiền
    $('body').on('change', 'input.c_quantity, input.c_price, input.c_discount, input.c_cost_trans, input.c_type_discount', function(e){
        var Tr = $(this).parents('tr.strMain');
        var quantity = intVal(Tr.find('input.c_quantity').val());
        var price = intVal(Tr.find('input.c_price').val());
        var discount = intVal(Tr.find('input.c_discount').val());
        var type_discount = Tr.find('input.c_type_discount:checked').val();
        var cost_trans = intVal(Tr.find('input.c_cost_trans').val());
        var total = 0;
        if(type_discount == 1)
        {
            total = (quantity * price) - (quantity * price * (discount/100) ) + cost_trans;
        }
        else if(type_discount == 2)
        {
            total = (quantity * price) - (discount) + cost_trans;
        }
        Tr.find('p.c_total').html(C_formatNumber(total));
        ReALL();
    })


    //Tính tổng lại
    function ReALL()
    {
        var TableItemBody = $('#table-item-quotes_orders tbody');
        var Tr = TableItemBody.find('tr');
        var CountItems = 0;
        var total = 0; // tổng thanh toán
        var total_cost_trans = 0; // tổng chi phú vận chuyển
        $.each(Tr, function(Ki,Vv){
            if($(Vv).find('select.c_product_items').val() || $(Vv).find('input.c_product_items').val())
            {
                CountItems++;

                var quantity = intVal($(Vv).find('input.c_quantity').val());
                var price = intVal($(Vv).find('input.c_price').val());
                var discount = intVal($(Vv).find('input.c_discount').val());
                var type_discount = $(Vv).find('input.c_type_discount:checked').val();
                var cost_trans = intVal($(Vv).find('input.c_cost_trans').val());
                if(type_discount == 1)
                {
                    total += (quantity * price) - (quantity * price * (discount/100) ) + cost_trans;
                }
                else if(type_discount == 2)
                {
                    total += (quantity * price) - (discount) + cost_trans;
                }

                total_cost_trans += cost_trans;

            }
        })
        $('p.total_cost_trans').html(C_formatNumber(total_cost_trans));
        $('p.c_number_items').html(C_formatNumber(CountItems));
        $('p.total_quotes_orders').html(C_formatNumber(total));
    }

    function orderTR()
    {
        var Tr = $('#table-item-quotes_orders tbody tr');
        $.each(Tr, function(i, v){
            $(v).find('td:nth-child(1)').text(i+1);
        })
    }


    function ViewShippingClient(idSelect)
    {
        var userid = $('select#client').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['client'] = userid;
        data['idSelect'] = idSelect;
        $.post(admin_url+'quotes_orders/ViewModalShipping', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('#cong_modal').html(data.data);
            }
            else
            {
                alert_float(data.alert_type, data.message);
            }
        })
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
