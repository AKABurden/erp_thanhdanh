<div class="modal fade" id="advance" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
			<?php
			$disabled = array();
			if (isset($items)) {
				$disabled = array('disabled' => true);
			}
			echo form_open(admin_url('advance/pay_slip/'), array('id' => 'payment-form'));
			?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_advance'); ?> </span>
                </h4>
            </div>
            <div class="modal-body" style="height:auto">
				<?php
				if (isset($items)) {
					?>
                    <input type="text" name="id_orther" class="hide" value="<?= $items->id ?>">
					<?php
				}
				?>
                <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                    <tbody>
                    <tr>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
								<?php echo _l('c_code_ctu'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="form-group">
								<?php $value = (isset($items) ? $items->prefix . '-' . $items->code : $code); ?>
                                <input type="text" id="code_vouchers" name="" class="form-control " readonly value="<?= $value ?>">
                            </div>
                        </td>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
								<?php echo _l('c_date_lap'); ?>
                            </label>
                        </td>
                        <td>
							<?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
							<?php echo render_date_input('date', '', $value); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
								<?php echo _l('c_object_nhan'); ?>
                            </label>
                        </td>
                        <td>
							<?php $list_objects = array(
								array('id' => 3,
									'name' => _l('ch_IN_staff'))
							); ?>
							<?php $value = (isset($items) ? $items->objects : ''); ?>
							<?php echo render_select('objects', $list_objects, array('id', 'name'), '', $value, $disabled,[],'','',false); ?>
                        </td>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger hide ch_list_objects">* </small>
								<?php echo _l('c_list_object'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="append_id_object">
                                <input data-placeholder="<?= _l('c_list_object') ?>" name="objects_id" style="width: 100%" id="objects_id">
                            </div>
                            <div class="ch_list_object hide">
                                <div class="form-group id">
                                    <input type="text" id="objects_text" name="objects_text" class="form-control objects_text" value="<?= (!empty($items) ? $items->objects_text : '') ?>">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
								<?php echo _l('c_code_group_chi_phi'); ?>
                            </label>
                        </td>
                        <td><div class="costs_code_parent"></div></td>
                        <td>
                            <label for="date" class="control-label">
								<?php echo _l('c_name_group_chi_phi'); ?>
                            </label>
                        </td>
                        <td><div class="costs_name_parent"></div></td>
                    </tr>
                    <tr>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
								<?php echo _l('c_code_muc_chi_phi'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="form-group">
                                <select id="id_costs" name="id_costs" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <?php
                                        $id_costs = (isset($items) ? $items->id_costs : 95);
                                        if(!empty($costs_list)) {
                                            foreach ($costs_list as $key => $value) {?>
                                                <?php if(!empty($value['name']) && !empty($value['data'])) {?>
                                                        <optgroup label="<?=$value['name']?>">
                                                            <?php foreach($value['data'] as $k => $v) {?>
                                                                <option value="<?=$v['id']?>"
                                                                    <?=$id_costs == $v['id'] ? 'selected' : ''?>
                                                                        data-subtext="<?=$v['name']?>"
                                                                        data-name_parent="<?=!empty($cost_parent[$v['costs_parent']]) ? $cost_parent[$v['costs_parent']]['name'] : ''?>"
                                                                        data-code_parent="<?=!empty($cost_parent[$v['costs_parent']]) ? $cost_parent[$v['costs_parent']]['code'] : ''?>"
                                                                >
                                                                    <?=$v['code']?>
                                                                </option>
                                                            <?php } ?>
                                                        </optgroup>
                                                <?php } ?>
                                            <?php }
                                        }
                                    ?>
                                </select>
                            </div>
<!--							--><?php //echo render_select('id_costs', $costs, array('id', 'name'), '', $id_costs); ?>
                        </td>
                        <td>
                            <label for="date" class="control-label">
								<?php echo _l('c_name_muc_chi_phi'); ?>
                            </label>
                        </td>
                        <td><div class="name_costs"></div></td>
                    </tr>
                    <tr class="hide">
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
								<?php echo _l('ch_type_of_document'); ?>
                            </label>
                        </td>
                        <td>
							<?php $type_vouchers = array(); ?>
							<?php if (isset($items) && $items->objects == 2) {
								$type_vouchers = array(
									array(
										'id' => 1,
										'name' => _l('ch_purchase_order_ck'),
									),
									array(
										'id' => 2,
										'name' => _l('ch_export_other_ck'),
									),
									array(
										'id' => 9,
										'name' => _l('order_production_details'),
									),
									array(
										'id' => 8,
										'name' => _l('ch_return_ck'),
									),
								);
							} elseif (isset($items) && $items->objects != 2) {
								$type_vouchers = array(
									array(
										'id' => 1,
										'name' => _l('ch_purchase_order_ck'),
									),
									array(
										'id' => 2,
										'name' => _l('ch_export_other_ck'),
									),
									array(
										'id' => 5,
										'name' => _l('ch_order_ck'),
									),
									array(
										'id' => 8,
										'name' => _l('ch_return_ck'),
									),
								);
							}
							?>
                            <select class="selectpicker no-margin" data-width="100%" id="type_vouchers" data-none-selected-text="<?php echo _l('Loại chứng từ'); ?>" name="type_vouchers" data-live-search="true" <?= (isset($items) ? 'disabled' : '') ?> >
                                <option value=""></option>
								<?php foreach ($type_vouchers as $product) { ?>
                                    <option <?= (isset($items) ? (($product['id'] == $items->type_vouchers) ? 'selected' : '') : '') ?> value="<?php echo $product['id']; ?>" data-subtext=""><?php echo $product['name']; ?></option>
									<?php
								} ?>
                            </select>
                        </td>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
								<?php echo _l('ch_list_code'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="vouchers_id_select">
                                <select class="selectpicker no-margin" data-width="100%" id="vouchers_id" data-none-selected-text="<?php echo _l('ch_list_code'); ?>" name="vouchers_id" data-live-search="true" <?= (isset($items) ? 'disabled' : '') ?> >
                                    <option value=""></option>
									<?php foreach ($vouchers_id as $product) { ?>
                                        <option <?= (isset($items) ? (($product['id'] == $items->vouchers_id) ? 'selected' : '') : '') ?> value="<?php echo $product['id']; ?>" total-id="<?= $product['total_import'] ?>" data-subtext=""><?php echo $product['name']; ?> ( <?php echo number_format($product['total_import']) ?> )</option>
										<?php
									} ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
								<?php echo _l('expense_add_edit_amount'); ?>
                            </label>
                        </td>
                        <td>
							<?php $total = (isset($items) ? number_format($items->total) : 0); ?>
                            <input type="text" id="votes_total" onkeyup="formatNumBerKeyUp(this)" name="total" class="form-control " value="<?= $total ?>">
                        </td>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
								<?php echo _l('acs_sales_payment_modes_submenu'); ?>
                            </label>
                        </td>
                        <td>
							<?php $value_payment_modes = (isset($items) ? $items->payment_modes : ''); ?>
							<?php echo render_select('payment_modes', $payment_modes, array('id', 'name'), '', $value_payment_modes); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
								<?php echo _l('note'); ?>
                            </label>
                        </td>
                        <td colspan="3">
							<?php $notes = (isset($items) ? $items->note : ''); ?>
                            <textarea rows="3" id="note" name="note" class="form-control" value=""><?= $notes ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <!-- data-loading-text="<?= _l('wait_text') ?>" -->
                <button type="submit" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                <!--  -->
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#id_costs').change(function () {
        var name_cost = $(this).find('option:selected').data('subtext');
        var costs_name_parent = $(this).find('option:selected').data('name_parent');
        var costs_code_parent = $(this).find('option:selected').data('code_parent');
        console.log(name_cost)
        $('.name_costs').text(name_cost);
        $('.costs_name_parent').text(costs_name_parent);
        $('.costs_code_parent').text(costs_code_parent);
    })

    $('#id_costs').trigger('change');

    function validate_form() {
        _validate_form($('#payment-form'), {
            code_vouchers: "required",
            date: "required",
            objects: "required",
            payment_modes: "required",
            payment: "required",
            objects_id: "required",
            id_costs: "required",
            total: "required",
        }, add_payment);
    }
    $(function () {
        validate_form();
    });
    $("#objects").change(function () {
        $('#objects_id').val('');
        $('#objects_id').selectpicker('refresh');
        $('#vouchers_id').attr('disabled', false);
        $('#type_vouchers').attr('disabled', false);
        $('#objects_id').attr('disabled', false);
        $('.ch_list_object').addClass('hide');
        $('.append_id_object').addClass('hide');
        $('#objects_id').prop('required', false);
        var id = $('#objects').val();
        var id_objects_id = 0;
        var type_vouchers = 0;
		<?php
		if(!empty($items))
		{?>
        id_objects_id = <?=$items->objects_id;?>;
        type_vouchers = <?=$items->type_vouchers;?>;
		<?php
		}
		?>
        var selectedd = '';
        if (id == 1) {
			<?php
			$type_vouchers = array(
				array(
					'id' => 1,
					'name' => _l('ch_purchase_order_ck'),
				),
				array(
					'id' => 2,
					'name' => _l('ch_export_other_ck'),
				),
				array(
					'id' => 5,
					'name' => _l('ch_order_ck'),
				),
				array(
					'id' => 8,
					'name' => _l('ch_return'),
				)
			);
			?>
            $('#type_vouchers').find('option:gt(0)').remove();
			<?php foreach ($type_vouchers as $product) { ?>
            var selectedd = '';
            if (type_vouchers == <?=$product['id'];?>) {
                selectedd = 'selected';
            }
            $('#type_vouchers').append('<option ' + selectedd + ' value="<?php echo $product['id']; ?>" ><?php echo $product['name']; ?></option>');
			<?php } ?>
            $('#type_vouchers').selectpicker('refresh');
            $('#objects_id').prop('required', true);
            $('.append_id_object').removeClass('hide');
            $('.ch_list_object').addClass('hide');
            $('#objects_text').val(1);
            ajaxSelectCallBack($('#objects_id'), "<?=admin_url('advance/SearchClient')?>", id_objects_id);
        } else if (id == 2) {
            $('#objects_text').val(1);
            $('#objects_id').prop('required', true);
            $('.append_id_object').removeClass('hide');
            $('.ch_list_object').addClass('hide');
            ajaxSelectCallBack($('#objects_id'), "<?=admin_url('advance/SearchClient')?>", id_objects_id);
			<?php
			$type_vouchers = array(
				array(
					'id' => 1,
					'name' => _l('ch_purchase_order_ck'),
				),
				array(
					'id' => 2,
					'name' => _l('ch_export_other_ck'),
				),
				array(
					'id' => 9,
					'name' => _l('order_production_details'),
				),
				array(
					'id' => 8,
					'name' => _l('ch_return'),
				),
				array(
					'id' => 65,
					'name' => _l('Service_ticket'),
				),
			);
			?>
            $('#type_vouchers').find('option:gt(0)').remove();
			<?php foreach ($type_vouchers as $product) { ?>
            var selectedd = '';
            if (type_vouchers == <?= $product['id'];?>) {
                selectedd = 'selected';
            }
            $('#type_vouchers').append('<option ' + selectedd + ' value="<?php echo $product['id']; ?>" ><?php echo $product['name']; ?></option>');
			<?php } ?>
            $('#type_vouchers').selectpicker('refresh');
        } else if (id == 3) {
            $('#objects_text').val(1);
            $('#objects_id').prop('required', true);
			<?php
			$type_vouchers = array(
				array(
					'id' => 1,
					'name' => _l('ch_purchase_order_ck'),
				),
				array(
					'id' => 2,
					'name' => _l('ch_export_other_ck'),
				),
				array(
					'id' => 5,
					'name' => _l('ch_order_ck'),
				),
				array(
					'id' => 8,
					'name' => _l('ch_return'),
				),
				array(
					'id' => 9,
					'name' => _l('order_production_details'),
				),
                array(
					'id' => 12,
					'name' => _l('ch_suggestion'),
				),
			);
			?>
            $('#type_vouchers').find('option:gt(0)').remove();
			<?php foreach ($type_vouchers as $product) { ?>
            var selectedd = '';
            if (type_vouchers == <?=$product['id'];?>) {
                selectedd = 'selected';
            }
            $('#type_vouchers').append('<option ' + selectedd + ' value="<?php echo $product['id']; ?>" ><?php echo $product['name']; ?></option>');
			<?php } ?>
            $('#type_vouchers').selectpicker('refresh');
            $('.append_id_object').removeClass('hide');
            $('.ch_list_object').addClass('hide');
            ajaxSelectCallBack($('#objects_id'), "<?=admin_url('advance/SearchClient')?>", id_objects_id);
        } else if (id == 4) {
			<?php
			$type_vouchers = array(
				array(
					'id' => 1,
					'name' => _l('ch_purchase_order_ck'),
				),
				array(
					'id' => 2,
					'name' => _l('ch_export_other_ck'),
				),
				array(
					'id' => 5,
					'name' => _l('ch_order_ck'),
				),
				array(
					'id' => 8,
					'name' => _l('ch_return'),
				),
				array(
					'id' => 9,
					'name' => _l('order_production_details'),
				),
                array(
					'id' => 12,
					'name' => _l('ch_suggestion'),
				),
			);
			?>
            $('#type_vouchers').find('option:gt(0)').remove();
			<?php foreach ($type_vouchers as $product) { ?>
            var selectedd = '';
            if (type_vouchers == <?=$product['id'];?>) {
                selectedd = 'selected';
            }
            $('#type_vouchers').append('<option ' + selectedd + ' value="<?php echo $product['id']; ?>" ><?php echo $product['name']; ?></option>');
			<?php } ?>
            $('#type_vouchers').selectpicker('refresh');
            $('.vouchers_ids').find('button').addClass('no-drop-v2');
            $('.ch_list_object').removeClass('hide');
            $('#objects_id').val(1);
            if ($('#objects_text').val() == 1) {
                $('#objects_text').val('');
            }
        } else if (id == 5) {
            ajaxSelectCallBack($('#objects_id'), "<?=admin_url('advance/SearchClient')?>", id_objects_id);
            $('#vouchers_id').attr('disabled', true);
            $('#type_vouchers').attr('disabled', true);
            $('#objects_id').select2('disable');
            $('#objects_id').val(1);
            $('#objects_text').val(1)
        }
		<?php
		if(!empty($items))
		{?>
        $('#objects_id').attr('disabled', true);
		<?php
		}
		?>
    });
	<?php
	if(!empty($items))
	{?>
    $('#objects').change();
	<?php
	}
	?>

    // $(document).on('change', '#objects_id', function (event) {
    //     var objects_id = $('#objects_id').val();
    //     var objects = $('#objects').val();
    //     var type_vouchers = $('#type_vouchers').val();
    //     dataString = {
    //         type_vouchers: type_vouchers,
    //         objects_id: objects_id,
    //         objects: objects,
    //         [csrfData['token_name']]: csrfData['hash']
    //     };
    //     jQuery.ajax({
    //         type: "post",
    //         url: "<?=admin_url()?>advance/vouchers_id/",
    //         data: dataString,
    //         cache: false,
    //         success: function (data) {
    //             data = JSON.parse(data);
    //             $('#vouchers_id').find('option:gt(0)').remove();
    //             $.each(data, function (key, value) {
    //                 if (type_vouchers == 1) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses - value.amount_paid) + ' value="' + value.id + '">' + value.prefix + '-' + value.code + ' (' + formatNumber((value.total - value.price_other_expenses - value.amount_paid)) + ')</option>');
    //                 } else if (type_vouchers == 2) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total) + ' value="' + value.id + '">' + value.prefix + '-' + value.code + ' (' + formatNumber((value.total)) + ')</option>');
    //                 } else if (type_vouchers == 5) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses_delivery) + ' value="' + value.id + '">' + value.reference_no + ' (' + formatNumber((value.total - value.price_other_expenses_delivery)) + ')</option>');
    //                 } else if (type_vouchers == 8) {
    //                     $('#vouchers_id').append('<option total-id="" value="' + value.id + '">' + value.prefix + value.code + '</option>');
    //                 } else if (type_vouchers == 9) {
    //                     $('#vouchers_id').append('<option total-id="" value="' + value.id + '">' + value.reference_no + '</option>');
    //                 } else if (type_vouchers == 65) {
    //                     $('#vouchers_id').append('<option total-id="' + (value.total - value.payment) + '" value="' + value.id + '">' + value.prefix + value.code + ' (' + formatNumber(value.total - value.payment) + ')</option>');
    //                 } else if (type_vouchers == 12) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total) + ' value="' + value.id + '">' +  value.code + ' (' + formatNumber((value.total)) + ')</option>');
    //                 }
    //             });
    //             $('#vouchers_id').selectpicker('refresh');
    //         }
    //     });
    // });
    // $(document).on('change', '#type_vouchers', function (event) {
    //     var objects_id = $('#objects_id').val();
    //     var objects = $('#objects').val();
    //     var type_vouchers = $('#type_vouchers').val();
    //     if (type_vouchers == 65) {
	// 		<?php if (empty($items)) { ?>
    //         var htmlss = '<select class="selectpicker no-margin" data-width="100%" id="vouchers_id" data-none-selected-text="<?php echo _l('ch_list_code'); ?>" name="vouchers_id[]" multiple="1"  data-live-search="true" >\
    //         <option value=""></option>\
    //                     </select>';
    //         $('.vouchers_id_select').html(htmlss);
	// 		<?php } ?>
    //     }
    //     dataString = {
    //         type_vouchers: type_vouchers,
    //         objects_id: objects_id,
    //         objects: objects,
    //         [csrfData['token_name']]: csrfData['hash']
    //     };
    //     jQuery.ajax({
    //         type: "post",
    //         url: "<?=admin_url()?>advance/vouchers_id/",
    //         data: dataString,
    //         cache: false,
    //         success: function (data) {
    //             data = JSON.parse(data);
    //             $('#vouchers_id').find('option:gt(0)').remove();
    //             $.each(data, function (key, value) {
    //                 if (type_vouchers == 1) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses - value.amount_paid) + ' value="' + value.id + '">' + value.prefix + '-' + value.code + ' (' + formatNumber((value.total - value.price_other_expenses - value.amount_paid)) + ')</option>');
    //                 } else if (type_vouchers == 2) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total) + ' value="' + value.id + '">' + value.prefix + '-' + value.code + ' (' + formatNumber((value.total)) + ')</option>');
    //                 } else if (type_vouchers == 5) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses_delivery) + ' value="' + value.id + '">' + value.reference_no + ' (' + formatNumber((value.total - value.price_other_expenses_delivery)) + ')</option>');
    //                 } else if (type_vouchers == 8) {
    //                     $('#vouchers_id').append('<option total-id="" value="' + value.id + '">' + value.prefix + value.code + '</option>');
    //                 } else if (type_vouchers == 9) {
    //                     $('#vouchers_id').append('<option total-id="" value="' + value.id + '">' + value.reference_no + '</option>');
    //                 } else if (type_vouchers == 65) {
    //                     $('#vouchers_id').append('<option total-id="' + (value.total - value.payment) + '" value="' + value.id + '">' + value.prefix + value.code + ' (' + formatNumber(value.total - value.payment) + ')</option>');
    //                 } else if (type_vouchers == 12) {
    //                     $('#vouchers_id').append('<option total-id=' + (value.total) + ' value="' + value.id + '">' +  value.code + ' (' + formatNumber((value.total)) + ')</option>');
    //                 }
    //             });
    //             if (type_vouchers == 2) {
    //                 $('#vouchers_id').attr('disabled', false);
    //             }
    //             $('#vouchers_id').selectpicker('refresh');
    //         }
    //     });
    // });
    function add_payment(form) {
        var objects_id = $('#objects_id').val();
        var objects = $('#objects').val();
        var type_vouchers = $('#type_vouchers').val();
        if (objects == 2 && !empty(objects_id) && type_vouchers != 8 && type_vouchers != 9) {
            var total_limit = $('option:selected', $('#vouchers_id')).attr('total-id');
            //
            if (type_vouchers == 65) {
                var total_limit_service = $('option:selected', $('#vouchers_id'));
                var total_limit = 0;
                $.each(total_limit_service, function () {
                    total_service = Number($(this).attr('total-id'));
                    total_limit += total_service;
                });
            }
            //
            var total = unformat_number($('#votes_total').val());
            if (Number(total) < 0) {
                alert('<?=_l('Giá trị không hợp lệ')?>');
                return;
            }
            if (Number(total) > Number(total_limit)) {
                alert('<?=_l('Giá trị không hợp lệ')?>' + ' Bạn phải nhập nhỏ hơn hoặc bằng: ' + formatNumber(total_limit));
                return;
            }
        }
        if (type_vouchers == 12) {
            var total_limit = $('option:selected', $('#vouchers_id')).attr('total-id');
            var total = unformat_number($('#votes_total').val());
            if (Number(total) < 0) {
                alert('<?=_l('Giá trị không hợp lệ')?>');
                return;
            }
            if (Number(total) > Number(total_limit)) {
                alert('<?=_l('Giá trị không hợp lệ')?>' + ' Bạn phải nhập nhỏ hơn hoặc bằng: ' + formatNumber(total_limit));
                return;
            }
        }
        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function (form) {
            form = JSON.parse(form),
                alert_float(form.alert_type, form.message);
            if (form.success) {
                tAPI.draw('page');
                $('#advance').modal('hide');
            }
        }), !1
    }
    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2 = x2.substr(0, 2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };
    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    function ajaxSelectCallBack(element, url, id, types = '') {
        console.log(id);
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: url + '/' + id + '/' + $('#objects').val(),
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
                            type: $('#objects').val(),
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
                            type: $('#objects').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{code_client: '', id: '', text: 'No Match Found'}]};
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
    $(function (e) {
		<?php
		if(empty($items))
		{?>
        ajaxSelectCallBack($('#objects_id'), "<?=admin_url('advance/SearchClient')?>", 0);
		<?php
		}
		?>
    })
    function repoFormatSelection(state) {
        var id = $('#objects').val();
        if (id == 3) {
            return state.text;
        }
        return '[' + state.code_client + '] ' + state.text;
    }
</script>