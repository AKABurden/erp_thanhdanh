<?php echo form_open('admin/manufactures/export_supplies/'.$id, array('id'=>'export-supplies')); ?>
<div class="modal-dialog modal-lg" style="width: 80%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_requrest_export_of_supplies') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y H:i')), 'placeholder="'.lang('date').'" id="date" required class="form-control input-tip datetimepicker"'); ?>
                    </div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<?= lang('tnh_export_name', 'export_name') ?>
						<?php echo form_input('export_name', (isset($_POST['export_name']) ? $_POST['export_name'] : 'Đề nghị xuất vật tư '.$reference_no), 'placeholder="'.lang('tnh_export_name').'" id="export_name" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('note', 'note') ?>
						<textarea name="note" id="note" placeholder="<?= lang('note') ?>" class="form-control" rows="3"></textarea>
					</div>
				</div>
				<div class="col-md-12">
					<p class="text-danger">*<?= lang('tnh_only_save_tick_chonse') ?></p>
                    <div class="mbot10">
                        <select name="" id="stage_search" class="modal-select2" data-placeholder="<?= lang('tnh_stage') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?= recursive_stages() ?>
                        </select>
                    </div>
                    <div class="mbot10">
                        <input type="text" class="form-control" id="search-export-supplies" placeholder="<?= lang('search') ?>">
                    </div>
					<div class="table-responsive">
						<table id="tb-export-supplies" class="tnh-table table dataTable table-hover">
							<thead>
								<tr>
									<th style="width: 40px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th style="width: 40px;" class="text-center">
                                        <div class="checkbox-info checkbox" style="margin: auto;">
                                            <input type="checkbox" class="tick-all" id="tick-all" value="">
                                            <label for="tick-all"></label>
                                        </div>
                                    </th>
									<th style="width: 150px;"><?= lang('tnh_material_code') ?></th>
									<th style="width: 150px;"><?= lang('tnh_material_name') ?></th>
									<th style="width: 50px;" class="text-center"><?= lang('tnh_unit') ?></th>
                                    <th class="text-center" style="width: 120px;"><?= lang('tnh_quantity_request_exported') ?></th>
                                    <th class="text-center" style="width: 120px;"><?= lang('tnh_quantity_export') ?></th>
									<th style="width: 100px;" class="text-center hide"><?= lang('tnh_value_exchange') ?></th>
									<!-- <th style="width: 110px;" class="text-center"><?= lang('tnh_quantity_exchange') ?></th> -->
                                    <th style="width: 100px; background: #ffff00;" class="text-center"><?= lang('tnh_quantity_warehouses') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_leadtime') ?></th>
                                    <th class="text-center" style="width: 150px;"><?= lang('tnh_stage') ?></th>
								</tr>
							</thead>
							<tbody>
                                <?php foreach ($materials as $key => $value): ?>
                                    <?php
                                        $quantity_use = $value['quantity'];
                                        $type_item = $value['type_item'];
                                        $item_id = $value['item_id'];
                                        $unit_id = $value['unit_id'];
                                        $quantity_exported = $this->manufactures_model->quantitySuggestExportingItem($id, $type_item, $item_id, $unit_id)['quantity_exported'];
                                        $quantity_export = $quantity_use - $quantity_exported;
                                        if ($quantity_export < 0) $quantity_export = 0;
                                    ?>
                                    <tr style="cursor: pointer;">
                                        <td class="text-center tick">
                                            <?= ++$key ?>
                                            <input type="hidden" name="item_id[<?= $key ?>]" id="item_id[]" class="form-control" value="<?= $value['item_id'] ?>">
                                            <input type="hidden" name="unit_id[<?= $key ?>]" id="unit_id[]" class="form-control" value="<?= $value['unit_id'] ?>">
                                            <input type="hidden" name="type_item[<?= $key ?>]" id="type_item[]" class="form-control" value="<?= $value['type_item'] ?>">
                                            <input type="hidden" name="unit_parent_id[<?= $key ?>]" id="unit_parent_id[]" class="form-control" value="<?= $value['unit_parent_id'] ?>">
                                            <input type="hidden" name="quantity_exchange[<?= $key ?>]" id="quantity_exchange[]" class="form-control quantity_exchange" value="<?= $value['quantity_exchange'] ?>">
                                        </td>
                                        <td class="text-center">
                                            <div class="checkbox-info checkbox" style="margin: auto;">
                                                <input name="tick_save[]" type="checkbox" class="tick_save" id="<?= $key ?>" value="<?= $key ?>">
                                                <label for="<?= $key ?>"></label>
                                            </div>
                                        </td>
                                        <td class="tick">
                                            <div><?= $value['item_code'] ?></div>
                                            <?php if ($value['type_item'] == 'semi_products_outside'): ?>
                                                <div style="margin-bottom: 5px;"></div>
                                                <div class="label label-danger" style="margin-top: 5px;"><?= lang('semi_products_outside') ?></div>
                                            <?php endif ?>
                                            <?php if ($value['type_item'] == 'semi_products'): ?>
                                                <div style="margin-bottom: 5px;"></div>
                                                <div class="label label-warning" style="margin-top: 5px;"><?= lang('semi_products') ?></div>
                                            <?php endif ?>
                                        </td>
                                        <td class="tick"><?= $value['item_name'] ?></td>
                                        <td class="text-center tick"><?= $value['unit_name'] ?></td>
                                        <td class="text-center tick"><?= formatNumber($quantity_exported) ?></td>
                                        <td>
                                            <input type="text" name="quantity_export[<?= $key ?>]" id="quantity_export[]" class="form-control quantity_export number-format" value="<?= formatNumber($quantity_export) ?>">
                                        </td>
                                        <td class="text-center tick hide"><?= $value['quantity_exchange'] ?></td>
                                        <!-- <td class="text-center quantity-primary tick"><?= formatNumber($quantity_export * $value['quantity_exchange']) ?></td> -->
                                        <td <?= $value['quantity_warehouse'] > 0 ? 'style="background: #ffff0085"' : '' ?> class="text-center tick"><?= formatNumber($value['quantity_warehouse']) ?></td>
                                        <td class="text-center">
                                            <?php
                                                $strLT = "";
                                                if (!empty($value['leadtime'])) {
                                                    $lt = explode('</br>', $value['leadtime']);
                                                    foreach ($lt as $k => $val) {
                                                        $plusDate = date('d/m/Y', strtotime($production_detail['date_created']. " + $val days"));
                                                        $strLT.= '<div><b>'.$val.'</b> ('.$plusDate.')'.'</div>';
                                                    }
                                                }
                                                echo $strLT;
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $value['stage'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
							</tbody>
						</table>
					</div>
                    <div class="pull-right">
                        <ul class="tpagination">
                        </ul>
                    </div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="save" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
			<button type="submit" name="add" class="btn btn-primary add"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        init_datepicker();
        $('#stage_search').select2({allowClear: true});
        // init_editor('#note');
        // $('#preview-purchase').DataTable({
        //     "language": app.lang.datatables,
        //     "pageLength": intVal(app.options.tables_pagination_limit),
        //     "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
        //     // 'searching': true,
        //     // 'ordering': true,
        //     // 'paging': true,
        //     // "info": true,
        //     "initComplete": function(settings, json) {
        //         var t = this;
        //         t.parents('.table-loading').removeClass('table-loading');
        //         t.removeClass('dt-table-loading');
        //         mainWrapperHeightFix();
        //     },
        // });

        tpanigation('#tb-export-supplies', 1);
        searchTableCustom('#tb-export-supplies', '#search-export-supplies', '.tpagination');
        createPanigation('#tb-export-supplies', '.tpagination');

        $('#tb-export-supplies tbody tr td.tick').click(function(event) {
            trClick = $(this).closest('tr');
            trClick.find('.tick_save').trigger('click');
        });

        $("#tick-all").click(function(){
            $('.tick_save').not(this).prop('checked', this.checked);
        });

        $('.quantity_export').change(function(event) {
            event.preventDefault();
            tr_current = $(this).closest('tr');
            quantity_export = intVal($(this).val());
            quantity_exchange = intVal(tr_current.find('.quantity_exchange').val());
            quantity_primary = quantity_export/quantity_exchange;
            tr_current.find('.quantity-primary').html(tnhFormatNumber(quantity_primary));
        });

       	appValidateForm($('#export-supplies'), {
            'date': 'required',
            'export_name': 'required'
        }, convert);

        function convert(form) {
        	$('.add').attr('disabled', 'disabled');
        	var url = form.action;
        	for (var i = 0; i < tinymce.editors.length; i++) {
        		tinymce.editors[i].save();
        	}

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
            		if (typeof dtSuggest != 'undefined' && dtSuggest != '') {
            			dtSuggest.draw();
            		}
            		$('.modal-dialog .close').trigger('click');
            	} else {
            		alert_float('danger', data.message);
            		$('.add').removeAttr('disabled', 'disabled');
            	}
            })
            .fail(function() {
            	alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }
    })

    $('#stage_search').change(function(event) {
        dataStage = event.added;
        if (typeof dataStage != "undefined") {
            dataStageText = dataStage.text.trim();
            $('#search-export-supplies').val(dataStageText).trigger('keyup');
        } else {
            $('#search-export-supplies').val('').trigger('keyup');
        }
        $(this).val('');
    });
</script>