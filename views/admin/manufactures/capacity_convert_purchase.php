<?php echo form_open('admin/manufactures/capacity_convert_purchase/'.$id, array('id'=>'add-purchase')); ?>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_convert_purchases'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y H:i')), 'placeholder="'.lang('date').'" id="date" required class="form-control input-tip datetimepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('ch_name_p', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : lang('ch_purchases')), 'placeholder="'.lang('ch_name_p').'" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('ch_note_t', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="'.lang('ch_note_t').'" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
                <!-- <div class="col-md-12 mbot10">
                    <a href="javascript:void(0)" value="<?= $id ?>" class="btn btn-danger cal-purchases"><?= lang('tnh_cal_purchases') ?></a>
                </div> -->
                <div class="col-md-12">
                    <table id="preview-purchase" class="dt-tnh table table-hover dont-responsive-table">
                        <thead>
                            <tr>
                                <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                <th><?= lang('code') ?></th>
                                <th><?= lang('name') ?></th>
                                <th><?= lang('type') ?></th>
                                <th class="text-center"><?= lang('unit') ?></th>
                                <th class="text-center"><?= lang('tnh_quantity_purchase') ?></th>
                                <!-- <th><?= lang('tnh_unit_exchange') ?></th>
                                <th class="text-center"><?= lang('tnh_quantity_exchange') ?></th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productions_capacity_items)): ?>
                                <?php foreach ($productions_capacity_items as $key => $value): ?>
                                    <?php
                                        $type = $value['type_sub'];
                                        $exchange = lang('no');
                                        $quantity_exchange = $value['quantity_exchange'];
                                        $quantity = $value['quantity_purchase_sub'];
                                        if ($type == "materials") {
                                            $item = $this->items_model->rowMaterial($value['id_sub']);
                                            $unit = $this->unit_model->rowUnit($item['unit_id']);
                                            $exchange = $unit['unit'].'/'.$value['quantity_exchange'];
                                            $quantity = $value['quantity_purchase_sub']/$quantity_exchange;
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td><?= $value['code_sub'] ?></td>
                                        <td><?= $value['name_sub'] ?></td>
                                        <td><?= lang($value['type_sub']) ?></td>
                                        <td class="text-center"><?= $value['unit'] ?></td>
                                        <td class="text-center"><?= formatNumber($value['quantity_purchase_sub']) ?></td>
                                        <!-- <td class="text-center"><?= $exchange ?></td>
                                        <td class="text-center"><?= formatNumber($quantity) ?></td> -->
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
			</div>
		</div>
		<div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        init_datepicker();
        dtItems = $('#preview-purchase').DataTable({
            "language": app.lang.datatables,
            "pageLength": intVal(app.options.tables_pagination_limit),
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            // 'searching': true,
            // 'ordering': true,
            // 'paging': true,
            // "info": true,
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });

        $('.cal-purchases').click(function(event) {
            event.preventDefault();
            return;
            productions_capacity_id = $(this).attr('value');
            if (productions_capacity_id > 0) {
                bootbox.confirm({
                    message: '<?= lang('tnh_you_want_cal_purchases') ?>',
                    buttons: {
                        confirm: {
                            label: '<?= lang('yes') ?>',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: '<?= lang('no') ?>',
                            className: 'btn-danger'
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            $.ajax({
                                url: site.base_url+'admin/manufactures/calPurchases/',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    productions_capacity_id: productions_capacity_id,
                                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                                },
                            })
                            .done(function(response) {
                                if (response) {
                                    if (response.result) {
                                        alert_float('success', response.message);
                                        dtItems.rows().remove().draw();
                                        $.each(response.items, function(index, el) {
                                            dtItems.row.add( [
                                                '<div class="text-center">'+(++index)+'</div>',
                                                '<div>'+el['code_sub']+'</div>',
                                                '<div>'+el['name_sub']+'</div>',
                                                '<div>'+lang_core[el['type_sub']]+'</div>',
                                                '<div class="text-center">'+el['unit']+'</div>',
                                                '<div class="text-center">'+tnhFormatNumber(el['quantity_purchase_sub'])+'</td>'
                                            ] ).draw( false ).node();
                                        });
                                    } else {
                                        alert_float('danger', 'fail');
                                    }
                                }
                            })
                            .fail(function() {
                                console.log("error");
                            });
                        }
                    }
                });
            }
        });


       	appValidateForm($('#add-purchase'), {
            'date': 'required'
        }, convert);

        function convert(form) {
        	$('.add').attr('disabled', 'disabled');
            // var data = $(form).serialize();
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
            //
            var url = form.action;
            $.ajax({
            	url : site.base_url+'admin/manufactures/capacity_convert_purchase/<?= $id ?>',
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
            		if (typeof oTable != 'undefined' && oTable != '') {
            			oTable.draw();
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
</script>