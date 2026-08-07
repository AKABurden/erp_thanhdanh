<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="currency_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 999999;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo $title ?></span>
                </h4>
            </div>
			<?php echo form_open('admin/currencies/manage_update', array('id' => 'currency_form')); ?>
			<?php
			    $value = !empty($currencies) ? $currencies->id : '';
                echo form_hidden('currencyid', $value);
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning"><?php echo _l('currency_valid_code_help'); ?></div>
						<?php
						$value = !empty($currencies) ? $currencies->name : '';
						echo render_input('name', 'currency_add_edit_description', $value, 'text', array('placeholder' => _l('iso_code')));
						?>
						<?php
						$value = !empty($currencies) ? $currencies->symbol : '';
						echo render_input('symbol', 'currency_add_edit_rate', $value);
						?>
						<?php
						$value = !empty($currencies) ? number_format_data($currencies->amount_to_vnd) : '0';
						echo render_input('amount_to_vnd', 'amount_to_vnd', ($value), 'text', array('onchange' => 'formatNumBerKeyUpCus(this)'));
						?>
               
                        <div class="row hide">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="decimal_separator"><?php echo _l('settings_sales_decimal_separator'); ?></label>
                                    <select id="decimal_separator" class="selectpicker" name="decimal_separator" data-width="100%">
                                        <option value=",">,</option>
                                        <option value="." selected>.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="thousand_separator"><?php echo _l('settings_sales_thousand_separator'); ?></label>
                                    <select id="thousand_separator" class="selectpicker" name="thousand_separator" data-width="100%" data-show-subtext="true">
                                        <option value="," selected>,</option>
                                        <option value=".">.</option>
                                        <option value="'" data-subtext="apostrophe">'</option>
                                        <option value="" data-subtext="none">&nbsp;</option>
                                        <option value=" " data-subtext="space">&nbsp;</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group hide">
                            <label for="placement" class="control-label clearfix"><?php echo _l('settings_sales_currency_placement'); ?></label>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" name="placement" value="before" id="c_placement_before">
                                <label for="c_placement_before"><?php echo _l('settings_sales_currency_placement_before'); ?></label>
                            </div>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" name="placement" id="c_placement_after" value="after" checked>
                                <label for="c_placement_after"><?php echo _l('settings_sales_currency_placement_after'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#currency_modal').modal('show');
        appValidateForm($('#currency_form'), {
            name: {
                required: true,
                maxlength: 3
            },
            symbol: 'required',
            decimal_separator: 'required',
            thousand_separator: 'required',
            placement: 'required',
            amount_to_vnd: 'required',
        }, manage_currencies);

        $('.exchange-time').daterangepicker({
            opens: 'top',
            drops: 'up',
            autoUpdateInput: true,
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
        }, function (start, end, label) {
        });


    });

    /* CURRENCY MANAGE FUNCTIONS */
    function manage_currencies(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('#currency').html(response.html);
                $('#currency').change();
                $('#ch_currency_pay_ment').html(response.html);
                $('#ch_currency_pay_ment').change();
            }
            $('#currency_modal').modal('hide');
        });
        return false;
    }

    var unique = <?=!empty($countItems) ? $countItems : 0?>;
    $('.btn-add-rate-exchange-time').click(function (e) {
        var current = $(e.currentTarget);
        var html = '<div class="row_items row">\
		                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">\
                            <div class="form-group">\
                                <label for="rate-exchange-time" class="control-label"><?=_l('ch_date_p')?></label>\
                                <div class="input-group">\
                                    <input type="text" id="rate-exchange-time-' + unique + '" name="item[' + unique + '][rate_exchange_time]" class="form-control" aria-invalid="false">\
                                    <div class="input-group-addon">\
                                        <i class="fa fa-calendar calendar-icon"></i>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">\
                            <div class="form-group">\
                                <label for="amount_rate_exchange_time" class="control-label"><?=_l('rate_exchange')?></label>\
                                <input type="text" id="amount_rate_exchange_time_' + unique + '" name="item[' + unique + '][amount_rate_exchange_time]" class="form-control" onchange="formatNumBerKeyUp(this)" value="0">\
                            </div>\
                        </div>\
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">\
                            <div class="remove_time_currencies mtop30 text-danger"><i class="fa fa-times"></i></div>\
                        </div>\
                    </div>\
					<div class="clearfix"></div>';
        $('.add-rate-exchange-time').append(html);

        $('#rate-exchange-time-' + unique).daterangepicker({
            opens: 'top',
            drops: 'up',
            autoUpdateInput: true,
            isInvalidDate: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                //"minDate": "<?//= date('m/d/Y')?>//",
                // "maxDate": "12/18/2019",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function (start, end, label) {
        });
        $('#rate-exchange-time-' + unique).val('').datepicker("refresh");
        unique++;
    });



    $('.remove_time_currencies').click(function (e) {
        $(this).parents('.row_items').remove();
    })

</script>
</body>
</html>
