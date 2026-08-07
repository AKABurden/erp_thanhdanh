<div class="modal fade" id="edit_coupon_support" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('add_coupon_support'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/coupon_support/edit/'.$id ,array('id'=>'form_add')); ?>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="tnh-tb table-bordered table-hover m-group0" style="table-layout: fixed;">
                        <tbody>
                            <tr>
                                <td style="width: 15%">
                                    <label for="appointment_date" class="control-label">
                                        <small class="req text-danger">* </small>
                                        <?php echo _l('coupon_support_date'); ?>
                                    </label>
                                </td>
                                <td style="width: 30%">
                                    <?php echo render_datetime_input('appointment_date','',_dt($dataMain->appointment_date)); ?>
                                </td>
                                <td style="width: 15%">
                                    <label for="customer_id" class="control-label">
                                        <small class="req text-danger">* </small>
                                        <?php echo _l('clients'); ?>
                                    </label>
                                </td>
                                <td style="width: 40%">
                                    <div class="form-group">
                                        <input type="text" name="customer_id" data-placeholder="<?= lang('customers') ?>" id="customer_id" class="customer_id" style="width: 100%;" value="<?= $dataMain->customer_id ?>">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="method" class="control-label">
                                        <?php echo _l('method'); ?>
                                    </label>
                                </td>
                                <td>
                                    <div class="radio radio-primary">
                                        <input type="radio" name="method" value="1" <?= $dataMain->method == 1 ? 'checked' : '' ?>>
                                        <label for="single"><?=_l('method1')?></label>
                                    </div>
                                    <div class="radio radio-primary">
                                        <input type="radio" name="method" value="2" <?= $dataMain->method == 2 ? 'checked' : '' ?>>
                                        <label for="single"><?=_l('method2')?></label>
                                    </div>
                                    <div class="radio radio-primary">
                                        <input type="radio" name="method" value="3" <?= $dataMain->method == 3 ? 'checked' : '' ?>>
                                        <label for="single"><?=_l('method3')?></label>
                                    </div>
                                </td>
                                <td>
                                    <label for="employees" class="control-label">
                                        <small class="req text-danger">* </small>
                                        <?php echo _l('tnh_employees_charge'); ?>
                                    </label>
                                </td>
                                <td>
                                    <?php
                                        $this->db->select('tblstaff.staffid, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname');
                                        $this->db->where('tblstaff.active', 1); 
                                        $staff = $this->db->get('tblstaff')->result_array(); 
                                    ?>
                                    <?php echo render_select('employees', $staff, array('staffid', 'fullname'),'',$dataMain->employees); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="method" class="control-label">
                                        <?php echo _l('note'); ?>
                                    </label>
                                </td>
                                <td colspan="3">
                                    <?php echo render_textarea('note','', $dataMain->note); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    ajaxSelectCustomerFormatTableCallBack('#customer_id', 'admin/clients/searchCustomers', $('#customer_id').val());
    $('select[name="employees"]').selectpicker('refresh');
});
function ajaxSelectCustomerFormatTableCallBack(element, url, id)
{
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatCustomer,
            escapeMarkup: function(m) {
                return m;
            },
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatCustomer,
            // formatSelection: formatTable,
            escapeMarkup: function(m) {
                return m;
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    }
}

appValidateForm($('#form_add'), {appointment_date: 'required', customer_id: 'required'}, manage_add);
function manage_add(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float(response.alert_type, response.message);
            tAPI.draw('page');
        }
        $('#edit_coupon_support').modal('hide');
    });
    return false;
}
</script>