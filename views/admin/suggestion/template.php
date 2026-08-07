<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .mtop54 {
        <?php if (!isset($invoice)) { ?> margin-top: 54px;
        <?php } else { ?> margin-top: 24px;
    <?php } ?>
    }

    legend {
        font-size: 15px;
        font-weight: 500;
        width: auto !important;
    }

    fieldset {
        padding: .35em .625em .75em !important;
        margin: 0 2px !important;
        border: 1px solid #19a9ea !important;
    }
</style>
<div class="panel_s invoice accounting-template">
    <div class="additional"></div>
    <div class="panel-body">
		<?php hooks()->do_action('before_render_invoice_template'); ?>
        <div class="row">
            <div class="col-md-12">
                <fieldset>
                    <legend><?= _l('Thông tin') ?></legend>
					<?php
					$events = '';
					if (isset($invoice)) {
						$events = 'disabled';
					} ?>
                    <div class="col-md-3">
						<?php $value = (isset($invoice) ? _d($invoice->date) : _d(date('Y-m-d'))); ?>
						<?php echo render_date_input('date', 'ch_service_date', $value); ?>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="number" class="control-label"><?php echo _l('ch_service_code'); ?></label>
							<?php
							$number = sprintf('%06d', ch_getMaxID('id', 'tblsuggestion') + 1);
							$value = (isset($invoice) ? ($invoice->code) : 'DX-' . $number);
							?>
                            <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
						<?php
						$type_items = array(
							array(
								'id' => 1,
								'name' => 'Mua vật tư'
							),
							array(
								'id' => 2,
								'name' => 'Tạm ứng'
							),
							array(
								'id' => 3,
								'name' => 'Thanh toán'
							),
							array(
								'id' => 4,
								'name' => 'Tạm ứng & Thanh toán'
							),
						);
						$value = (isset($invoice) ? ($invoice->type) : 1);
						echo render_select('type', $type_items, array('id', 'name'), 'Loại', $value, [], [], '', '', false);
						?>
                    </div>
                    <div class="col-md-3">
						<?php
						$type_items = array(
							array(
								'id' => 1,
								'name' => 'Gấp',
								'sub' => 'Xử lý lập tức'
							),
							array(
								'id' => 2,
								'name' => 'Bình Thường',
								'sub' => 'Xủ lý trong vòng 2 ngày làm việc'
							)
						);
						$value = (isset($invoice) ? ($invoice->status) : '');
						echo render_select('status', $type_items, array('id', 'name', 'sub'), 'Trạng thái', $value);
						?>
                    </div>
                    <div class="from_tu_tt">
                        <div class="col-md-3">
                            <label for="staffid" class="control-label"><?php echo _l('ch_staff_suggestion'); ?></label>
							<?php $staffid = (isset($invoice) ? ($invoice->staffid) : 0); ?>
                            <input data-placeholder="<?= _l('ch_staff_suggestion') ?>" value="<?= $staffid ?>" name="staffid" style="width: 100%" id="staffid">

                            <div class="mtop20">
                                <label for="staff_browse" class="control-label"><?php echo _l('Người duyệt chi'); ?></label>
                                <?php $staff_browse = (isset($invoice) ? ($invoice->staff_browse) : 0); ?>
                                <input data-placeholder="<?= _l('Người duyệt chi') ?>" value="<?= $staff_browse ?>" name="staff_browse" style="width: 100%" id="staff_browse">
                            </div>


                        </div>
                        <div class="col-md-3">
                            <label for="staffid" class="control-label"><?php echo _l('ch_total_suggestion'); ?></label>
							<?php
							$price = (isset($invoice) ? formatNumber($invoice->price_total) : 0);
							?>
                            <input id="price_total" name="price" class="form-control text-right price_total" onchange="formatNumBerKeyUp(this)" placeholder="<?php echo _l('ch_total_suggestion'); ?>" value="<?= $price ?>">

                            <div class="mtop20">
								<?php $value = (isset($invoice) ? ($invoice->id_payment_modes) : 0);?>
								<?= render_select('id_payment_modes', (!empty($payment_modes) ? $payment_modes : []), ['id', 'name', 'description'], 'Hình thức thanh toán', $value);?>
                            </div>
                        </div>
                        <div class="col-md-3">
							<?php $value = (isset($invoice) ? $invoice->note : ''); ?>
							<?php echo render_textarea('note', 'ch_note_suggestion', $value, array('rows' => 3)); ?>
                        </div>
                        <div class="col-md-3">
                            <div id="div_upload">
                                <div id="dropzoneFeedback" class="dropzoneDragArea dz-default dz-message feedback-comment-dropzone">
                                    <span class="icon_upload_img"><?php echo _l('drop_files_here_to_upload'); ?></span>
                                </div>
                            </div>
                            <div class="dropzone-task-comment-previews dropzone-previews"></div>
                            <!-- <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="checkfile" id="checkfile">
                                <label for="checkfile">Kèm theo chứng từ</label>
                            </div>
                            <div>
                                <input data-placeholder="<?= _l('ch_staff_suggestion') ?>" readonly class="form-control " value="" name="text_checkfile" style="width: 100%" id="text_checkfile">
                            </div> -->
                        </div>
						<?php if (isset($invoice)) { ?>
                            <div class="col-md-12">
								<?php $j = 0; ?>
								<?php foreach ($invoice->file as $key => $value) { ?>
									<?php if (substr($value['filetype'], 0, 5) != 'image') { ?>
                                        <div class="media lead-note file_<?= $key ?>">
                                            <div class="media-body"><i class="mime mime-file"></i>
                                                <a href="<?= base_url() ?>uploads/suggestion/<?= $invoice->id ?>/<?= $value['file_name'] ?>">
													<?= $value['file_name'] ?>
                                                </a>
                                                <a href="#" style="margin-left: 14px;" class="text-danger" onclick="delete_file(<?= $key ?>,<?= $value['id'] ?>);return false;">
                                                    <i class="fa fa fa-times"></i>
                                                </a>
                                                <hr>
                                            </div>
                                        </div>
										<?php $j++;
									}
								}
								?>
                            </div>
                            <div class="col-md-12">
								<?php foreach ($invoice->file as $key => $value) { ?>
									<?php if (substr($value['filetype'], 0, 5) == 'image') { ?>
                                        <div class="preview_image id_images<?= $key ?>" id="images_product_view" style="width: auto;float: left;margin: 0 !important;margin-left: 10px;">
                                            <div class="display-block contract-attachment-wrapper img-1">
                                                <a href="<?= base_url() ?>uploads/suggestion/<?= $invoice->id ?>/<?= $value['file_name'] ?>" data-lightbox="customer-pos" class="display-block mbot5 show-images" product_id="<?= $invoice->id ?>">
                                                    <div>
                                                        <img src="<?= base_url() . 'uploads/suggestion/' . $invoice->id . '/' . $value['file_name'] ?>" style="width:100px;height:100px;" class="img-rounded ">
                                                    </div>
                                                </a>
                                                <a href="#" class="pull-right text-danger" onclick="delete_file_images(<?= $key ?>,<?= $value['id'] ?>);return false;">
                                                    <i class="fa fa fa-times" style="top:0px;right: 0px;color: red;"></i>
                                                </a></div>
                                        </div>
										<?php $j++;
									} ?>
								<?php }
								?>
                            </div>
						<?php } ?>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="panel-body mtop10 ">
		<?php if (isset($invoice_from_project)) {
			echo '<hr class="no-mtop" />';
		} ?>
        <div class="table-responsive s_table">
            <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                <tr>
                    <th width="5%" align="center">
                        <a onclick="change_item_load_v3()" class="btn btn-info btn-icon"><?= _l('ch_service_add_colum') ?></a>
                    </th>
                    <th width="30%" align="center"><?php echo _l('Vật tư'); ?></th>
                    <th width="5%" align="center"><?php echo _l('tnh_dvt'); ?></th>
                    <th width="10%" align="center"><?php echo _l('Quy cách'); ?></th>
                    <th width="10%" align="center"><?php echo _l('ch_service_quanliti_in'); ?></th>
                    <th width="20%" align="center"><?php echo _l('ch_service_price_in'); ?></th>
                    <th width="20%" align="center"><?php echo _l('ch_service_toal_in'); ?></th>
                    <th align="center"><i class="fa fa-cog"></i></th>
                </tr>
                </thead>
                <tbody>
				<?php
				$i = 0;
				if (isset($invoice)) {
					foreach ($invoice->item as $item) {
						$items_detail = get_items($item['id_items'], $item['type']);
						$manual = false;
						$table_row = '<tr class="sortable">';
						$table_row .= '<td class="text-center count"></td>';
						$table_row .= '<td class=""><input type="hidden" class="count" value="' . $i . '" />
                                    <input type="hidden" class="type_item"  name="items[' . $i . '][type]" value="' . ($item['type']) . '" />
                                <input style="width:350px;" data-placeholder="' . _l('dropdown_non_selected_tex') . '" class="custom_item_select" data-id="' . ($item['id_items']) . '" id="custom_item_select_' . $i . '" name="items[' . $i . '][ustom_item_select]" style="width: 100%"></td>';
						$table_row .= '<td class="text-center unit_name">' . $items_detail->unit_name . '</td>';
						$table_row .= '<td class="mode_name">' . $items_detail->mode . '</td>';
						$table_row .= '<td class="text-right"><input  id="quanliti" name="items[' . $i . '][quanliti]" class="form-control text-center quanliti" onchange="formatNumBerKeyUpCus(this)" placeholder="Số lượng" value="' . formatNumber($item['quantity']) . '"></td>';
						$table_row .= '<td class="text-right"><input  id="price" name="items[' . $i . '][price]" class="form-control text-right price" onchange="formatNumBerKeyUpCus(this)" placeholder="Đơn giá" value="' . formatNumber($item['price']) . '"></td>';
						$table_row .= '<td class="text-right subtotalss">' . formatNumber($item['amount']) . '</td><td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>';
						$table_row .= '</tr>';
						echo $table_row;
						$i++;
					}
				}
				?>
                </tbody>
            </table>
        </div>
        <div class="col-md-8 col-md-offset-4">
            <table class="table text-right">
                <tbody>
                <tr>
                    <td style="width:300px"><span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
                    </td>
                    <td class="quantili_all">0
                    </td>
                </tr>
                <tr>
                    <td><span class="bold"><?php echo _l('Tổng thành tiền'); ?> :</span>
                    </td>
                    <td style="width:300px" class="total_all">0
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="removed-items"></div>
        <div id="billed-tasks"></div>
        <div id="billed-expenses"></div>
    </div>
    <div class="row">
        <div class="col-md-12 mtop15">
            <div class="panel-body bottom-transaction">
                <div class="btn-bottom-toolbar text-right">
                    <div class="btn-group dropup">
                        <button class="btn-tr btn btn-info invoice-form-submit "><?php echo _l('Lưu phiếu'); ?></button>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-pusher"></div>
        </div>
    </div>
</div>
<script>
	<?php if (!empty($invoice)) { ?>
    setTimeout(function () {
        $('table.invoice-items-table tbody').find('.selectpicker').selectpicker('refresh').change();
    }, 700);
	<?php } ?>

    function se_contract() {
        var id = jQuery('#clientid').val()
        var dataString = {
            id: id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>invoices/load_bill_client",
            data: dataString,
            cache: false,
            success: function (data) {
                if (data == "<option></option>") {
                    if (id != "") {
                        alert_float('danger', "<?php echo _l('Không tìm thấy phiếu xuất bill nào') ?>");
                        $('#id_contract').html('');
                    } else {
                        $('#id_contract').html('');
                    }
                } else {
                    $('#select_bill').html(data).selectpicker('refresh');
                    alert_float('success', "<?php echo _l('Tìm thấy hợp đồng chưa xuất hóa đơn') ?>");
                }
            }
        });
    }

    var i = <?= $i ?>;

    function change_item_load_v3() {
        i++;
        var _td = _td + '<td class="text-center count"></td>';
        _td = _td + '<td class=""><input type="hidden" class="count" value="' + i + '" />\
                <input type="hidden" class="type_item"  name="items[' + i + '][type]" value="0" />\
        	<input style="width:35  0px;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select_' + i + '" name="items[' + i + '][ustom_item_select]" style="width: 100%"></td>';
        _td = _td + '<td class="text-center unit_name"></td>';
        _td = _td + '<td class="mode_name"></td>';
        _td = _td + '<td class="text-right"><input  id="quanliti" name="items[' + i + '][quanliti]" class="form-control text-center quanliti" onchange="formatNumBerKeyUp(this)" placeholder="Số lượng" value="0"></td>';
        _td = _td + '<td class="text-right"><input  id="price" name="items[' + i + '][price]" class="form-control text-right price" onchange="formatNumBerKeyUp(this)" placeholder="Đơn giá" value="0"></td>';
        _td = _td + '<td class="text-right subtotalss">0</td><td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>';
        $('table.invoice-items-table tbody').prepend('<tr class="tr_">' + _td + '</tr>');
        // $(".ui-sortable").html('<tr class="tr_'+data.id+'">' + _td + '</tr>');
        // tinh_tien(data.id);
        $('table.invoice-items-table tbody').find('.selectpicker').selectpicker('refresh');
        $('select.tax_ch').change();
        ajaxSelectCallBacks($('#custom_item_select_' + i), "<?= admin_url('suggestion/SearchItems_ch') ?>", 0);
        countrow();
    }

    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };
    countrow();

    function countrow() {
        var items = $('table.invoice-items-table tbody').find('tr');
        var dem = items.length;
        $.each(items, (index, value) => {
            $(value).find('td:nth-child(1)').text(dem);
            dem--;
        });
    }

    function delete_colum(id) {
        $(".tr_" + id).remove();
        $('#select_bill').val('');
    }

    // $('.invoice-form-submit').on('click', (e) => {
    //     var items = $('table.invoice-items-table tbody tr');
    //     if (items.length == 0) {
    //         alert_float('danger', '<?= _l('Hàng hóa - dịch vụ không được để rỗng') ?>');
    //         return;
    //     }
    //     if ($('input.error').length) {
    //         e.preventDefault();
    //         alert('<?= _l('Hàng hóa - dịch vụ không được để rỗng') ?>');
    //         return;
    //     } else {
    //         $('#invoicesch-form').submit();
    //     }
    // });
    function ajaxSelectCallBack_hau(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                width: 'resolve',
                allowClear: false,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val(),
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
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [
                                    {
                                        id: '',
                                        text: 'No Match Found'
                                    }
                                ]
                            };
                        }
                    }
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                ajax: {
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [
                                    {
                                        code_client: '',
                                        id: '',
                                        text: 'No Match Found'
                                    }
                                ]
                            };
                        }
                    }
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }
    }

    $(function (e) {
        ajaxSelectCallBack_hau($('#staffid'), "admin/suggestion/SearchStaff", $('#staffid').val());

        ajaxSelectCallBack_hau($('#staff_browse'), "admin/suggestion/SearchStaff", $('#staff_browse').val());
    });
    $(document).on('change', '#checkfile', (e) => {
        $('#text_checkfile').attr('readonly', true);
        var remember = document.getElementById("checkfile");
        if (remember.checked) {
            $('#text_checkfile').attr('readonly', false);
        } else {
            $('#text_checkfile').val('');
        }
    });
    $(document).on('change', '#type', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var type = $(currentQuantityInput).val();
        // $('.table_items_vt').addClass('hide');
        $('.price_total').attr('readonly', false);
        if (type == 1) {
            $('.price_total').attr('readonly', true);
        }
        // getTotalPrice();
    });
    $('#type').change();

    function ajaxSelectCallBacks(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [
                                    {
                                        id: '',
                                        text: 'No Match Found'
                                    }
                                ]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [
                                    {
                                        code_client: '',
                                        id: '',
                                        text: 'No Match Found'
                                    }
                                ]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }
    }

    var base_url = '<?= base_url() ?>';

    function repoFormatSelection(state) {
        if (!state.id) return state.text;
        return state.text + ' - ' + '(' + state.code + ')';
    }
</script>