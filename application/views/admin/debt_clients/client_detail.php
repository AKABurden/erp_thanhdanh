<style type="text/css">
    .dt-table-loading.table {
        opacity: unset !important;
    }

    .table-detail_client thead tr th {
        text-align: center;
    }

    .table-detail_client tr td:nth-child(1) {
        text-align: center;
        width: 50px;
    }

    .table-detail_client tr td:nth-child(2) {
        white-space: unset;
        text-align: center;
    }

    .table-detail_client tr td:nth-child(3) {
        white-space: unset;
        text-align: center;
        min-width: 100px;
    }

    .table-detail_client tr td:nth-child(4) {
        white-space: unset;
        text-align: center;
        min-width: 100px;
    }

    .table-detail_client tr td:nth-child(5) {
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }

    .table-detail_client tr td:nth-child(6) {
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }

    .table-detail_client tr td:nth-child(7) {
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }

    .table-detail_client tr td:nth-child(8) {
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }

    .table-detail_client tr td:nth-child(9) {
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }

    .table-detail_client tr td:nth-child(10) {
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }

    td.details-control {
        background: url('../assets/images/tnh/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url('../assets/images/tnh/details_close.png') no-repeat center center;
    }
</style>
<div class="modal fade in" id="client_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo $title ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <p class="bold"><?php echo _l('filter_by'); ?></p>
                                </div>
                                <div class="col-md-3">
									<?php
									echo render_date_input('date_start', 'report_sales_from_date');
									?>
                                </div>
                                <div class="col-md-3">
									<?php
									echo render_date_input('date_end', 'report_sales_to_date');
									?>
                                </div>
                                <div class="col-md-6 hide">
									<?php if (has_permission('vouchers_coupon', '', 'create')) { ?>
                                        <div class="pull-left" style="margin-top: 30px;">
                                            <a class="btn btn-info  " data-toggle="collapse" data-target="#search">
												<?php echo _l('ch_create_vouchers_coupon'); ?>
                                            </a>
                                        </div>
									<?php } ?>
                                </div>
                                <div class="col-md-12">
									<?php echo form_open(admin_url('vouchers_coupon/add_all/'), array('id' => 'payment-form')); ?>
                                    <input type="text" name="id_clients" value="<?= $uerid ?>" class="hide">
                                    <fieldset id="search" style="float: right;" class="collapse">
                                        <legend><?= _l('coupon') ?></legend>
                                        <input type="text" name="code_orders" id="idd" class="hide">
                                        <div style="padding: 8px; border: 1px solid #03a9f4; word-wrap: break-word;margin-top: -21px;">
                                            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                                <tbody>
                                                <tr>
                                                    <td class="hide" style="width: 17%;">
                                                        <label for="number" class="control-label">
                                                            <small class="req text-danger">* </small>
															<?php echo _l('ch_code_p'); ?>
                                                        </label>
                                                    </td>
                                                    <td class="hide">
                                                        <div class="form-group">
															<?php $value = (isset($items) ? $items->prefix . '-' . $items->code : $code); ?>
                                                            <input type="text" id="code_vouchers" name="" class="form-control " readonly value="<?= $value ?>">
                                                        </div>
                                                    </td>
                                                    <td style="width: 11%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
															<?php echo _l('ch_date_p'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
														<?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
														<?php echo render_date_input('date_vouchers', '', $value); ?>
                                                    </td>
                                                    <td style="width: 11%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
															<?php echo _l('acs_sales_payment_modes_submenu'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
														<?php $value_payment_modes = (isset($items) ? $items->payment_modes : ''); ?>
														<?php echo render_select('payment_mode', $payment_modes, array('id', 'name'), '', $value_payment_modes); ?>
                                                    </td>
                                                    <td style="width: 6%;">
                                                        <label for="number" class="control-label">
															<?php echo _l('note'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
														<?php $notes = (isset($items) ? $items->note : ''); ?>
                                                        <textarea rows="3" id="note" name="note" class="form-control" value=""><?= $notes ?></textarea>
                                                    </td>
                                                </tr>
                                                <tr class="hide">
                                                    <td style="width: 17%;">
                                                        <label for="number" class="control-label">
                                                            <small class="req text-danger">* </small>
															<?php echo _l('expense_add_edit_amount'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
														<?php $total = (isset($items) ? number_format($items->total) : 0); ?>
                                                        <input type="text" readonly id="votes_total" onkeyup="formatNumBerKeyUp(this)" name="payment" class="form-control " value="<?= $total ?>">
                                                        <input type="text" readonly id="totals" onkeyup="formatNumBerKeyUp(this)" name="total" class="form-control " value="<?= $total ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="clearfix"></div>
                                            <br>
                                            <div class="text_wanring" style="float: left;width: 80%;text-align: center;">
                                                <span style="color: red;font-size: 20px" class="bold text-center"><?= _l('ch_chose_orders') ?></span>
                                            </div>
                                            <div style="float: right;">
                                                <button type="button" id="ch_close" style="float: right;" class="btn btn-danger" data-toggle="collapse" data-target="#search"><?= _l('ch_close') ?></button>
                                                <button type="submit" style="float: right;margin-right: 5px;" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                                            </div>
                                            <div class="clearfix"></div>
                                    </fieldset>
                                </div>
                                </form>
                                <div class="clearfix"></div>
                                <hr>
                                <input type="hidden" id="filterType" name="filterType" value="<?= !empty($filterType) ? $filterType : '' ?>"/>
								<?php $table_data = array(
									_l('+'),
									_l('ch_date_p'),
									_l('ch_code_p'),
									_l('invoice_table_tax_heading'),
									_l('tnh_money_tax'),
									_l('ch_debt_total'),
									_l('ch_reduce_cost'),
									_l('ch_status_receipts_slip'),
									_l('ch_receipts_other'),
									_l('ch_total_left'),
								);
								render_datatable($table_data, 'detail_client');
								?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var tAPIss
        function format(data, row, tr) {
            $.ajax({
                url: '<?=admin_url('debt_clients/show_daital')?>',
                type: 'POST',
                dataType: 'html',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                    data: data,
                },
            })
            .done(function (data) {
                row.child(data).show();
                tr.addClass('shown');
            })
            .fail(function () {
                console.log("error");
            });
        }
        $(function () {
            var CustomersServerParams = {
                'date_start': '[name="date_start"]',
                'date_end': '[name="date_end"]',
                'filterType': '[name="filterType"]',
            };
            tAPIss = initDataTableFixedHeader_hau('.table-detail_client', admin_url + 'debt_clients/table_detail_client/' + <?=$uerid?>, [1], [0], CustomersServerParams, [1, 'asc']);
            $.each(CustomersServerParams, function (filterIndex, filterItem) {
                $('' + filterItem).on('change', function () {
                    tAPIss.ajax.reload();
                });
            });
        });
        $('.table-detail_client').on('draw.dt', function () {
            var rows = $('.table-detail_client').find('tbody tr');
            var total = 0;
            var idd = '';
            $.each(rows, function () {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') === true) {
                    total += parseFloat(checkbox.attr('data-id'));
                    idd += checkbox.val() + ',';
                }
            });
            if (total <= 0) {
                $('.text_wanring').html('<span style="color: red;font-size: 20px" class="bold text-center"><?=_l('ch_chose_orders')?></span>');
            }
            $('#idd').val(idd);
            $('#totals').val(formatNumber(total));
            $('#votes_total').val(formatNumber(total));


            // var tr = $('.table-detail_client').find('tbody').find('tr');
            // $('.table-detail_client').find('tfoot').remove();
            // var htmlfooter = $('<tfoot></tfoot>');
            // var trTotal = $(`<tr></tr>`);
            // var tdTotal = $(`<th colspan="4">TỔNG CỘNG</th>`);
            // trTotal.append(tdTotal);
            // Total1 = 0;
            // Total2 = 0;
            // Total3 = 0;
            // Total4 = 0;
            // Total5 = 0;
            // Total6 = 0;
            // $.each(tr, function (index, value) {
            //     Total1 += intVal($(value).find('td:nth-child(5)').text());
            //     Total2 += intVal($(value).find('td:nth-child(6)').text());
            //     Total3 += intVal($(value).find('td:nth-child(7)').text());
            //     Total4 += intVal($(value).find('td:nth-child(8)').text());
            //     Total5 += intVal($(value).find('td:nth-child(9)').text());
            //     Total6 += intVal($(value).find('td:nth-child(10)').text());
            // })
            // var tdTotal1 = $(`<th class="text-right">${tnhFormatNumber(Total1)}</th>`);
            // var tdTotal2 = $(`<th class="text-right">${tnhFormatNumber(Total2)}</th>`);
            // var tdTotal3 = $(`<th class="text-right">${tnhFormatNumber(Total3)}</th>`);
            // var tdTotal4 = $(`<th class="text-right">${tnhFormatNumber(Total4)}</th>`);
            // var tdTotal5 = $(`<th class="text-right">${tnhFormatNumber(Total5)}</th>`);
            // var tdTotal6 = $(`<th class="text-right">${tnhFormatNumber(Total6)}</th>`);
            // trTotal.append(tdTotal1);
            // trTotal.append(tdTotal2);
            // trTotal.append(tdTotal3);
            // trTotal.append(tdTotal4);
            // trTotal.append(tdTotal5);
            // trTotal.append(tdTotal6);
            // htmlfooter.append(trTotal);
            // $('.table-detail_client').find('tbody').after(htmlfooter);
        });
        _validate_form($('#payment-form'), {
            code_vouchers: "required",
            date: "required",
            receiver: "required",
            objects: "required",
            payment_mode: "required",
            payment: "required",
            objects_id: "required",
            id_costs: "required",
            total: "required",
            objects_text: "required",
        }, add_payment_s);
        function add_payment_s(form) {
            var total = unformat_number($('#votes_total').val());
            if (total <= 0) {
                alert('<?=_l('ch_alert_check')?>');
                return;
            }
            var objects_id = $('#objects_id').val();
            var objects = $('#objects').val();
            var type_vouchers = $('#type_vouchers').val();
            var data = $(form).serialize(),
                action = form.action;
            return $.post(action, data).done(function (form) {
                form = JSON.parse(form),
                    alert_float(form.alert_type, form.message);
                tAPI.draw('page');
                $('#ch_close').click();
            }), !1
        }
    </script>
</div>